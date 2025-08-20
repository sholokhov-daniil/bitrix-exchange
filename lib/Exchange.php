<?php

namespace Sholokhov\Exchange;

use Bitrix\Main\Diag\Debug;
use Exception;
use Throwable;
use ReflectionException;

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Bootstrap\Validator;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\LoggerHelper;
use Sholokhov\Exchange\Events\EventManager;
use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\Events\Factory\AttributeEventFactory;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\ExchangeResult;
use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Repository\Result\ResultRepositoryInterface;
use Sholokhov\Exchange\Validators\ValidatorInterface;
use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Preparation\Chain;
use Sholokhov\Exchange\Preparation\PreparationInterface;
use Sholokhov\Exchange\Target\Attributes\Validate;
use Sholokhov\Exchange\Target\Attributes\MapValidator;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\NotImplementedException;

use Psr\Log\LoggerAwareTrait;

/**
 * Базовый класс обмена данными
 *
 *  <b>Методы отвечающие за конфигурацию обмена:</b>
 *  <ul>
 *      <li>{@see self::bootstrapEvents()} - Инициализация системных событий</li>
 *      <li>{@see self::bootstrapValidationMapping()} - Инициализация механизма валидации карты обмена</li>
 *      <li>{@see self::bootstrapRepository()} - Инициализация хранилища объекта обмена</li>
 *  </ul>
 *
 *  <b>Методы отвечающие за валидацию конфигурации обмена:</b>
 *  <ul>
 *      <li>{@see self::mapValidate()} - Проверка корректности карты обмена</li>
 *  </ul>
 *
 * @since 1.0.0
 * @version 1.1.0
 */
#[MapValidator]
abstract class Exchange extends Application implements MappingExchangeInterface
{
    use LoggerAwareTrait;

    /**
     * Хранилище данных текущего обмена
     *
     * @var RepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected readonly RepositoryInterface $repository;

    /**
     * Добавление нового элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function add(array $item): DataResultInterface;

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function update(array $item): DataResultInterface;

    /**
     * Проверка наличия элемента сущности
     *
     * @param array $item
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function exists(array $item): bool;

    /**
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function isMultipleField(FieldInterface $field): bool;

    /**
     * Деактивация элементов сущности, которые не пришли в обмене
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function deactivate(): void
    {
    }

    /**
     * Указание карты данных обмена
     *
     * @param FieldInterface[] $map
     * @return Exchange
     *
     * @throws Exception
     * @since 1.0.0
     * @version 1.1.0
     */
    public function setMap(array $map): static
    {
        $primaryExist = false;

        foreach ($map as $field) {
            if ($field->isPrimary()) {
                $primaryExist = true;
                $this->repository->set('primary_field', $field);
            }

            if ($field->isHash()) {
                $this->repository->set('hash_field', $field);
            }
        }

        if (!$primaryExist) {
            throw new Exception("No key field found");
        }

        $this->repository->set('map', $map);
        return $this;
    }

    /**
     * Получение хэша импорта
     *
     * @return string
     * @since 1.1.0
     * @version 1.1.0
     */
    public function getHash(): string
    {
        return (string)$this->getOptions()->get('hash', '');
    }

    /**
     * Запуск обмена
     *
     * @param iterable $source
     * @return ExchangeResultInterface
     *
     * @throws NotImplementedException
     * @throws ReflectionException
     * @since 1.0.0
     * @version 1.0.0
     */
    final public function execute(iterable $source): ExchangeResultInterface
    {
        $resultRepository = null;

        if (is_callable($this->getResultRepository())) {
            $resultRepository = call_user_func($this->getResultRepository(), $this);

            if (!$resultRepository instanceof ResultRepositoryInterface) {
                throw new NotImplementedException('Result repository not implemented: ' . ResultRepositoryInterface::class);
            }
        }

        $result = new ExchangeResult($resultRepository);
        $validate = $this->validate();

        if (!$validate->isSuccess()) {
            return $result->addErrors($validate->getErrors());
        }

        $this->repository->set('date_up', time());

        $this->getEventManager()->send(ExchangeEvent::BeforeRun->value);
        (new Event(Helper::getModuleID(),  ExchangeEvent::BeforeRun->value, ['exchange' => $this]))->send();

        try {
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
                    $result->getData()?->add($data);
                }
            }
        } catch (Throwable $throwable) {
            $result->addError(Error::createFromThrowable($throwable));
            $this->logger?->critical(LoggerHelper::exceptionToString($throwable));
        }

        if ($this->getOptions()->get('deactivate')) {
            $this->deactivate();
        }

        $this->getEventManager()->send(ExchangeEvent::AfterRun->value);
        (new Event(Helper::getModuleID(), ExchangeEvent::AfterRun->value, ['exchange' => $this]))->send();

        $this->repository->set('date_up', 0);

        return $result;
    }

    /**
     * ID сайта, которому принадлежит обмен
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getSiteID(): string
    {
        return (string)$this->getOptions()->get('site_id');
    }

    /**
     * Получение карты обмена
     *
     * @return FieldInterface[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getMap(): array
    {
        return $this->repository->get('map');
    }

    /**
     * Добавить преобразователь данных обмена
     *
     * @param PreparationInterface $prepare
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addPrepared(PreparationInterface $prepare): self
    {
        $this->getPrepares()->add($prepare);
        return $this;
    }

    /**
     * Указание генератора хранилища
     *
     * @final
     * @param callable $callback
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final public function setResultRepository(callable $callback): static
    {
        $this->getOptions()->set('result_repository', $callback);
        return $this;
    }

    /**
     * Получить время запуска обмена
     *
     * @final
     * @return int
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function getDateStarted(): int
    {
        return $this->repository->get('date_up', 0);
    }

    /**
     * Получение генератора хранилища
     *
     * @return callable|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getResultRepository(): ?callable
    {
        return $this->getOptions()->get('result_repository');
    }

    /**
     * Получение цепочки преобразователей данных
     *
     * @final
     * @return Chain
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function getPrepares(): Chain
    {
        return $this->repository->get('prepares');
    }

    /**
     * Проверка возможности запуска обмена данных
     *
     * @return ResultInterface
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function validate(): ResultInterface
    {
        return (new Validator($this))->run();
    }

    /**
     * Получение свойства отвечающего за идентификацию значения
     *
     * @final
     * @return FieldInterface
     * @throws Exception
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function getPrimaryField(): FieldInterface
    {
        return $this->repository->get('primary_field');
    }

    /**
     * Получение поля отвечающего за хеш
     *
     * @return FieldInterface|null
     * @since 1.1.0
     * @version 1.1.0
     */
    final protected function getHashField(): ?FieldInterface
    {
        return $this->repository->get('hash_field');
    }

    /**
     * Вызов действия над элементом источника
     *
     * @param array $item
     * @return DataResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function action(array $item): DataResultInterface
    {
        $this->getEventManager()->send(ExchangeEvent::BeforeImportItem->value, $item);
        (new Event(Helper::getModuleID(), ExchangeEvent::BeforeImportItem->value, ['exchange' => $this, 'item' => &$item]))->send();

        $prepareResult = $this->prepared($item);

        if (!$prepareResult->isSuccess()) {
            return $prepareResult;
        }

        $item = $prepareResult->getData();

        if ($this->exists($item)) {
            $this->getEventManager()->send(ExchangeEvent::BeforeUpdate->value, $item);
            $event = new Event(Helper::getModuleID(), ExchangeEvent::BeforeUpdate->value, ['exchange' => $this, 'item' => &$item]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() !== EventResult::SUCCESS) {
                    $this->logger?->debug('The updating of the element was rejected by the event: ' . json_encode($item));
                    return new DataResult;
                }
            }

            $result = $this->update($item);

            $this->getEventManager()->send(ExchangeEvent::AfterUpdate->value, $item, $result);
            (new Event(Helper::getModuleID(), ExchangeEvent::AfterUpdate->value, ['exchange' => $this, 'item' => $item, 'result' => $result]))->send();
        } else {
            $this->getEventManager()->send(ExchangeEvent::BeforeAdd->value, $item);
            $event = new Event(Helper::getModuleID(), ExchangeEvent::BeforeAdd->value, ['exchange' => $this, 'item' => &$item]);

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType()!== EventResult::SUCCESS) {
                    $this->logger?->debug('The creation of the element was rejected by the event: ' . json_encode($item));
                    return new DataResult;
                }
            }

            $result = $this->add($item);
            $this->getEventManager()->send(ExchangeEvent::AfterAdd->value, $item, $result);
            (new Event(Helper::getModuleID(), ExchangeEvent::AfterAdd->value, ['exchange' => $this, 'item' => $item, 'result' => $result]))->send();
        }

        $this->getEventManager()->send(ExchangeEvent::AfterImportItem->value, $item, $result);
        (new Event(Helper::getModuleID(), ExchangeEvent::AfterImportItem->value, ['exchange' => $this, 'item' => $item, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Преобразование значения
     *
     * @param array $item
     * @return DataResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function prepared(array $item): DataResultInterface
    {
        $result = new DataResult;
        $map = $this->getMap();
        $data = [];

        foreach ($map as $field) {
            $value = FieldHelper::getValue($item, $field);
            $value = $this->normalize($value, $field);


            if (is_callable($field->getPreparation())) {
                $value = call_user_func($field->getPreparation(), $value, $field);
            } else {
                $value = $this->getPrepares()->prepare($value, $field);
            }

            $data[$field->getTo()] = $value;
        }

        $result->setData($data);

        return $result;
    }

    /**
     * Нормализация импортированного значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function normalize(mixed $value, FieldInterface $field): mixed
    {
        $isMultiple = $this->isMultipleField($field);

        if ($isMultiple && !is_array($value)) {
            $value = is_null($value) ? [] : [$value];
        } elseif (!$isMultiple && is_array($value)) {
            $value = reset($value);
        }

        if (is_callable($field->getNormalizer())) {
            $value = call_user_func($field->getNormalizer(), $value, $field);
        }

        return $value;
    }

    /**
     * Получение менеджера событий
     *
     * @return EventManager
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function getEventManager(): EventManager
    {
        return $this->repository->get('event_manager');
    }

    /**
     * Валидация карты обмена
     *
     * @return ResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[Validate]
    private function mapValidate(): ResultInterface
    {
        return $this->repository
            ->get('map_validator')
            ->validate($this->getMap());
    }

    /**
     * Инициализация хранилища дополнительных данных обмена
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapRepository(): void
    {
        $this->repository = new Memory;
        $this->repository->set('prepares', new Chain);
    }

    /**
     * Инициализация механизма валидации карты обмена
     *
     * @return void
     * @throws ReflectionException
     * @throws Exception
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapValidationMapping(): void
    {
        /** @var MapValidator $attribute */
        $attribute = Entity::getAttributeChain($this, MapValidator::class);
        $validator = $attribute->getEntity();

        if (!is_subclass_of($validator, ValidatorInterface::class)) {
            throw new Exception('Validator class must be subclass of ' . ValidatorInterface::class);
        }

        $this->repository->set('map_validator', new $validator);
    }

    /**
     * Инициализация и регистрация событий
     * 
     * @return void
     *
     * @throws ReflectionException
     * @since 1.0.0
     * @version 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapEvents(): void
    {
        $manager = new EventManager;
        $events = (new AttributeEventFactory($this))->make();
        $this->repository->set('event_manager', $manager->registrationBulk($events));
    }
}
