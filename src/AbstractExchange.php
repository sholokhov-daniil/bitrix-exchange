<?php

namespace Sholokhov\Exchange;

use Exception;
use Iterator;
use ArrayIterator;
use ReflectionException;

use Sholokhov\Exchange\Events\Event;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Validators\ValidatorInterface;
use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Target\Attributes\MapValidator;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

#[MapValidator]
abstract class AbstractExchange extends Application
{
    use LoggerAwareTrait;

    private ResultInterface $result;

    private array $map = [];
    protected int $dateUp = 0;

    /**
     * События обмена
     *
     * @var Event
     */
    protected Event $event;

    /**
     * Добавление нового элемента сущности
     *
     * @param array $item
     * @return ResultInterface
     */
    abstract protected function add(array $item): ResultInterface;

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return ResultInterface
     */
    abstract protected function update(array $item): ResultInterface;

    /**
     * Проверка наличия элемента сущности
     *
     * @param array $item
     * @return bool
     */
    abstract protected function exists(array $item): bool;

    public function __construct(array $options = [])
    {
        $this->event = new Event;
        parent::__construct($options);
    }

    final public function execute(iterable $source): ResultInterface
    {
        $dataResult = [];
        $result = $this->check();
        if (!$result->isSuccess()) {
            return $result;
        }

        $this->dateUp = time();

//        try {
            foreach ($source as $item) {
                if (!is_array($item)) {
                    $this->logger?->warning('The source value is not an array: ' . json_encode($item));
                    continue;
                }

                $action = $this->action($item);
                if (!$action->isSuccess()) {
                    $result->addErrors($action->getErrors());
                }

                if ($data = $action->getData()) {
                    $dataResult[] = $data;
                }
            }

//        } catch (\Throwable $throwable) {
//            $this->result->addError(new Error($throwable->getMessage(), $throwable->getCode()));
//            $this->logger?->critical(LoggerHelper::exceptionToString($throwable));
//        }

        $this->event->invokeAfterRun();

            $this->dateUp = 0;

        // Удаление элементов, которые не обновились

        return $result->setData($dataResult);
    }

    /**
     * ID сайта, которому принадлежит обмен
     *
     * @return string
     */
    public function getSiteID(): string
    {
        return (string)($this->getOptions()->get('site_id') ?? \Bitrix\Main\Application::getInstance()->getContext()->getSite());
    }

    /**
     * Получение карты обмена
     *
     * @return FieldInterface[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Указание карты данных обмена
     *
     * @param array $map
     * @return AbstractExchange
     */
    public function setMap(array $map): static
    {
        $this->map = $map;
        return $this;
    }

    /**
     * Проверка возможности запуска обмена данных
     *
     * @return ResultInterface
     * @throws ReflectionException
     */
    protected function check(): ResultInterface
    {
        $result = new DataResult;

        $mapValidate = $this->mapValidate($this->getMap());
        if (!$mapValidate->isSuccess()) {
            $result->addErrors($mapValidate->getErrors());
        }

        return $result;
    }

    /**
     * Получение свойства отвечающего за идентификацию значения
     *
     * @return FieldInterface
     * @throws Exception
     */
    final protected function getKeyField(): FieldInterface
    {
        foreach ($this->getMap() as $field) {
            if ($field->isKeyField()) {
                return $field;
            }
        }

        throw new Exception("No key field found");
    }

    /**
     * Вызов действия над элементом источника
     *
     * @param array $item
     * @return ResultInterface
     */
    private function action(array $item): ResultInterface
    {
        $this->event->invokeBeforeActionItem();
        $normalizeResult = $this->normalize($item);

        if (!$normalizeResult->isSuccess()) {
            return $normalizeResult;
        }

        $item = $normalizeResult->getData();

        if ($this->exists($item)) {
            $result = $this->update($item);
            $this->event->invokeAfterUpdate(['ITEM' => $item]);
        } else {
            $result = $this->add($item);
            $this->event->invokeAfterAdd(['ITEM' => $item]);
        }

        $this->event->invokeAfterActionItem($item);

        return $result;
    }

    /**
     * Нормализация импортируемых данных, для восприятия системы
     *
     * @param array $item
     * @return ResultInterface
     */
    private function normalize(array $item): ResultInterface
    {
        $result = new DataResult;
        $fields = [];

        $map = $this->getMap();

        foreach ($map as $field) {
            $value = FieldHelper::getValue($item, $field);

            if ($field->isMultiple() && !is_array($value)) {
                $value = $value === null ? [] : [$value];
            }

            foreach ($field->getNormalizers() as $validator) {
                $value = call_user_func_array($validator, [$value, $field]);
            }

            $fields[$field->getCode()] = $value;
        }

        foreach ($map as $field) {
            if ($target = $field->getTarget()) {
                if ($this->logger && $target instanceof LoggerAwareInterface) {
                    $target->setLogger($this->logger);
                }

                if ($this->logger && $target instanceof LoggerAwareInterface) {
                    $target->setLogger($this->logger);
                }

                $source = new ArrayIterator([$item]);
                $targetResult = $target->execute($source);

                if (!$targetResult->isSuccess()) {
                    $result->addErrors($targetResult->getErrors());
                }

                $targetDataResult = $targetResult->getData();

                if ($field->isMultiple() && !is_array($targetDataResult)) {
                    $fields[$field->getCode()] = $targetDataResult === null ? [] : [$targetDataResult];
                } elseif (!$field->isMultiple() && is_array($targetDataResult)) {
                    $fields[$field->getCode()] = reset($targetDataResult);
                }
            }
        }

        return $result->setData($fields);
    }

    /**
     * Валидация карты обмена
     *
     * @param array $map
     * @return ResultInterface
     * @throws ReflectionException
     * @throws Exception
     */
    private function mapValidate(array $map): ResultInterface
    {
        /** @var MapValidator $attribute */
        $attribute = Entity::getAttribute($this, MapValidator::class) ?: Entity::getAttribute(self::class, MapValidator::class);
        $validator = $attribute->getEntity();

        if (!is_subclass_of($validator, ValidatorInterface::class)) {
            throw new Exception('Validator class must be subclass of ' . ValidatorInterface::class);
        }

        return (new $validator)->validate($map);
    }
}