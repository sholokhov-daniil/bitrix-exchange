<?php

namespace Sholokhov\Exchange;

use Exception;
use Iterator;
use ArrayIterator;
use ReflectionException;

use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Validators\Validator;
use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Target\Attributes\MapValidator;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

#[MapValidator]
abstract class AbstractExchange extends Application
{
    use LoggerAwareTrait;

    private Result $result;

    private array $map = [];

    abstract protected function add(array $item): Result;
    abstract protected function update(array $item): Result;
    abstract protected function exists(array $item): bool;

    final public function execute(Iterator $source): Result
    {
        $dataResult = [];
        $result = $this->check();
        if (!$result->isSuccess()) {
            return $result;
        }

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

                $dataResult[] = $action->getData();
            }

            $deactivate = $this->deactivate();
            if (!$deactivate->isSuccess()) {
                $result->addErrors($deactivate->getErrors());
            }
//        } catch (\Throwable $throwable) {
//            $this->result->addError(new Error($throwable->getMessage(), $throwable->getCode()));
//            $this->logger?->critical(LoggerHelper::exceptionToString($throwable));
//        }

        // Удаление элементов, которые не обновились

        return $result->setData($dataResult);
    }

    /**
     * Получение карты обмена
     *
     * @return Field[]
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
     * @return Result
     * @throws ReflectionException
     */
    protected function check(): Result
    {
        $result = new DataResult;

        $mapValidate = $this->mapValidate($this->getMap());
        if (!$mapValidate->isSuccess()) {
            $result->addErrors($mapValidate->getErrors());
        }

        return $result;
    }

    private function action(array $item): Result
    {
        // Событие перед импортом

        $item = $this->normalize($item);

        if ($this->exists($item)) {
            $result = $this->update($item);
        } else {
            $result = $this->add($item);
        }

        // Событие по окончанию

        return $result;
    }

    /**
     * Преобразование данных обмена
     *
     * @param mixed $value
     * @param Field $field
     * @return mixed
     */
    private function prepare(mixed $value, Field $field): mixed
    {
        $target = $field->getTarget();
        if (!$target) {
            return $value;
        }

        $source = $field->isMultiple() ? new ArrayIterator($value) : new ArrayIterator([$value]);

        if ($this->logger && $target instanceof LoggerAwareInterface) {
            $target->setLogger($this->logger);
        }

        return $target->execute($source)
                      ->getData();
    }

    /**
     * Нормализация импортируемых данных, для восприятия системы
     *
     * @param array $item
     * @return array
     */
    private function normalize(array $item): array
    {
        $result = [];

        foreach ($this->getMap() as $field) {
            $value = FieldHelper::getValue($item, $field);

            if ($field->isMultiple() && !is_array($value)) {
                $value = $value === null ? [] : [$value];
            }

            // TODO: Вызвать пользовательские нормализаторы
            $value = $this->prepare($value, $field);

            $result[$field->getCode()] = $value;
        }

        return $result;
    }

    /**
     * Валидация карты обмена
     *
     * @param array $map
     * @return Result
     * @throws ReflectionException
     * @throws Exception
     */
    private function mapValidate(array $map): Result
    {
        /** @var MapValidator $attribute */
        $attribute = Entity::getAttribute($this, MapValidator::class) ?: Entity::getAttribute(self::class, MapValidator::class);
        $validator = $attribute->getEntity();

        if (!is_subclass_of($validator, Validator::class)) {
            throw new Exception('Validator class must be subclass of ' . Validator::class);
        }

        return (new $validator)->validate($map);
    }
}