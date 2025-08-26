<?php

namespace Sholokhov\Exchange;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Sholokhov\Exchange\Builder\ExchangeResultBuilder;
use Sholokhov\Exchange\Dispatcher\EventDispatchableInterface;
use Sholokhov\Exchange\Dispatcher\EventDispatchableTrait;
use Sholokhov\Exchange\Factory\Exchange\ProcessorFactory;
use Sholokhov\Exchange\Factory\Exchange\ValidatorFactory;
use Sholokhov\Exchange\Processor\ProcessorInterface;
use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Target\Attributes\Event;
use Throwable;

use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Messages\Type\Error;

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
abstract class Exchange implements ExchangeInterface, EventDispatcherInterface
{
    use LoggerAwareTrait, EventDispatchableTrait;

    protected readonly ProcessorInterface $processor;
    protected readonly Memory $repository;
    protected readonly Memory $options;
    protected array $validators;

    public function __construct(array $options = [])
    {
        $this->processor = ProcessorFactory::create($this);
        $this->validators = ValidatorFactory::create($this);
        $this->repository = new Memory;
        $this->options = new Memory($options);
    }

    public function execute(iterable $source): ExchangeResultInterface
    {
        $result = $this->createResult();

        $this->validate($result);
        if (!$result->isSuccess()) {
            return $result;
        }

        $this->beforeRunEvent();

        try {
            if ($this->logger && $this->processor instanceof LoggerAwareInterface) {
                $this->processor->setLogger($this->logger);
            }

            $this->processor->run($source, $result);
        } catch (Throwable $e) {
            $this->handleException($e, $result);
        }

        $this->afterRunEvent();

        return $result;
    }

    /**
     * Получение конфигурации обмена
     *
     * @return ContainerInterface
     */
    public function getOptions(): ContainerInterface
    {
        return $this->options;
    }

    /**
     * Получение генератора хранилища
     *
     * @return callable|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getResultRepositoryFactory(): ?callable
    {
        return $this->getOptions()->get('result_repository') ?: null;
    }

    /**
     * Создание объекта хранения результатов обмена
     *
     * @return ExchangeResultInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function createResult(): ExchangeResultInterface
    {
        $factory = $this->getResultRepositoryFactory();
        return ExchangeResultBuilder::create($this, $factory);
    }

    /**
     * Обработка ошибки обмена
     *
     * @param Throwable $exception
     * @param ExchangeResultInterface $result
     * @return void
     */
    protected function handleException(Throwable $exception, ExchangeResultInterface $result): void
    {
        $this->logger?->error($exception->getMessage(), ['exception' => $exception]);
        $result->addError(new Error($exception->getMessage()));
    }

    /**
     * Валидация обмена
     *
     * @param ExchangeResultInterface $result
     * @return void
     */
    protected function validate(ExchangeResultInterface $result): void
    {
        foreach ($this->validators as $validator) {
            $validateResult = $validator->validate($this);

            if (!$validateResult->isSuccess()) {
                $result->addErrors($validateResult->getErrors());
            }
        }
    }

    /**
     * Событие перед запуском обмена
     *
     * @return void
     */
    private function beforeRunEvent(): void
    {
        $this->repository->set('date_up', time());
        $this->dispatch(new Events\Event(ExchangeEvent::BeforeRun->value));
    }

    /**
     * Событие после окончания обмена
     *
     * @return void
     */
    private function afterRunEvent(): void
    {
        $this->dispatch(new Events\Event(ExchangeEvent::AfterRun->value));
        $this->repository->set('date_up', 0);
    }
}
