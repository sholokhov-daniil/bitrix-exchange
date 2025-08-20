<?php

namespace Sholokhov\Exchange;

use Exception;
use Sholokhov\Exchange\Factory\MapValidatorFactory;
use Sholokhov\Exchange\Preparation\FieldPreparation;
use Sholokhov\Exchange\Repository\MapRepository;
use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Services\ItemProcessor;
use Throwable;
use ReflectionException;

use Sholokhov\Exchange\Bootstrap\Validator;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\LoggerHelper;
use Sholokhov\Exchange\Events\EventManager;
use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\Type\ExchangeResult;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Repository\Result\ResultRepositoryInterface;
use Sholokhov\Exchange\Preparation\PreparationInterface;
use Sholokhov\Exchange\Target\Attributes\MapValidator;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;

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
 */
#[MapValidator]
abstract class Exchange extends Application implements MappingExchangeInterface
{
    use LoggerAwareTrait;

    private readonly ItemProcessor $processor;

    private readonly Validator $validator;

    private readonly EventManager $events;

    private readonly MapRepository $mapRepository;

    /**
     * Хранилище данных текущего обмена
     *
     * @var RepositoryInterface
     */
    protected readonly RepositoryInterface $repository;

    /**
     * Добавление нового элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     */
    abstract public function add(array $item): DataResultInterface;

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     */
    abstract public function update(array $item): DataResultInterface;

    /**
     * Проверка наличия элемента сущности
     *
     * @param array $item
     * @return bool
     */
    abstract public function exists(array $item): bool;

    /**
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     */
    abstract public function isMultipleField(FieldInterface $field): bool;

    /**
     * Деактивация элементов сущности, которые не пришли в обмене
     *
     * @return void
     */
    protected function deactivate(): void
    {
    }

    /**
     * Запуск обмена
     *
     * @param iterable $source
     * @return ExchangeResultInterface
     *
     * @throws NotImplementedException
     * @throws ReflectionException
     */
    final public function execute(iterable $source): ExchangeResultInterface
    {
        $resultRepository = $this->createResultRepository();
        $result = new ExchangeResult($resultRepository);

        $validate = $this->validator->validate($this);
        if (!$validate->isSuccess()) {
            return $result->addErrors($validate->getErrors());
        }

        $this->repository->set('date_up', time());
        $this->events->send(ExchangeEvent::BeforeRun->value);

        try {
            foreach ($source as $item) {
                if (!is_array($item)) {
                    $this->logger?->warning('The source value is not an array: ' . json_encode($item));
                    continue;
                }

                $processResult = $this->processor->process($item);
                if (!$processResult->isSuccess()) {
                    $result->addErrors($processResult->getErrors());
                }

                if ($data = $processResult->getData()) {
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

        $this->events->send(ExchangeEvent::AfterRun->value);
        $this->repository->set('date_up', 0);

        return $result;
    }

    /**
     * Указание карты данных обмена
     *
     * @param FieldInterface[] $map
     * @return Exchange
     * @throws Exception
     */
    public function setMap(array $map): static
    {
        $this->mapRepository->setMap($map);
        return $this;
    }

    /**
     * Получение хэша импорта
     *
     * @return string
     */
    public function getHash(): string
    {
        return (string)$this->getOptions()->get('hash', '');
    }

    /**
     * ID сайта, которому принадлежит обмен
     *
     * @return string
     */
    public function getSiteID(): string
    {
        return (string)$this->getOptions()->get('site_id');
    }

    /**
     * Получение карты обмена
     *
     * @return FieldInterface[]
     */
    public function getMap(): array
    {
        return $this->mapRepository->getMap();
    }

    /**
     * Добавить преобразователь данных обмена
     *
     * @param PreparationInterface $prepare
     * @return $this
     */
    public function addPrepared(PreparationInterface $prepare): self
    {
        $this->getPreparation()->add($prepare);
        return $this;
    }

    /**
     * Указание генератора хранилища
     *
     * @final
     * @param callable $callback
     * @return $this
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
     */
    final protected function getDateStarted(): int
    {
        return $this->repository->get('date_up', 0);
    }

    /**
     * Получение генератора хранилища
     *
     * @return callable|null
     */
    protected function getResultRepository(): ?callable
    {
        return $this->getOptions()->get('result_repository');
    }

    /**
     * Получение цепочки преобразователей данных
     *
     * @final
     * @return FieldPreparation
     */
    final protected function getPreparation(): FieldPreparation
    {
        return $this->repository->get('prepares');
    }

    /**
     * Получение свойства отвечающего за идентификацию значения
     *
     * @final
     * @return FieldInterface
     * @throws Exception
     */
    final protected function getPrimaryField(): FieldInterface
    {
        return $this->mapRepository->getPrimaryField();
    }

    /**
     * Получение поля отвечающего за хеш
     *
     * @return FieldInterface|null
     */
    final protected function getHashField(): ?FieldInterface
    {
        return $this->mapRepository->getHashField();
    }

    /**
     * Создание хранилища результата обмена
     *
     * @return ResultRepositoryInterface|null
     * @throws NotImplementedException
     */
    private function createResultRepository(): ?ResultRepositoryInterface
    {
        $resultRepository = null;

        if (is_callable($this->getResultRepository())) {
            $resultRepository = call_user_func($this->getResultRepository(), $this);

            if (!($resultRepository instanceof ResultRepositoryInterface)) {
                throw new NotImplementedException('Result repository not implemented: ' . ResultRepositoryInterface::class);
            }
        }

        return $resultRepository;
    }

    /**
     * Конфигурация механизма обмена
     *
     * @return void
     * @throws ReflectionException
     */
    #[BootstrapConfiguration]
    private function configuration(): void
    {
        $this->repository = new Memory;
        $this->validator = new Validator;
        $this->events = EventManager::create($this);

        $this->processor = new ItemProcessor($this);
        $this->processor->setEventManager($this->events);

        $this->mapRepository = new MapRepository(MapValidatorFactory::create($this));
    }
}
