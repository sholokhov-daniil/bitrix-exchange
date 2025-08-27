<?php

namespace Sholokhov\Exchange;

use Throwable;
use ReflectionException;

use Sholokhov\Exchange\Builder\ExchangeResultBuilder;
use Sholokhov\Exchange\Dispatcher\EventDispatchableTrait;
use Sholokhov\Exchange\Factory\Exchange\ProcessorFactory;
use Sholokhov\Exchange\Factory\Exchange\ValidatorFactory;
use Sholokhov\Exchange\Processor\ProcessorInterface;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Target\Attributes\Event;
use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Messages\Type\Error;

use Bitrix\Main\NotImplementedException;

use Psr\Log\LoggerAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Базовый класс обмена данными
 */
abstract class AbstractExchange implements ExchangeInterface, EventDispatcherInterface
{
    use LoggerAwareTrait,
        EventDispatchableTrait;

    protected readonly ProcessorInterface $processor;
    protected readonly Memory $repository;
    protected readonly Memory $options;
    protected array $validators;
    protected readonly RepositoryInterface $cache;

    public function deactivate(): void
    {
    }

    public function __construct(array $options = [])
    {
        $this->processor = ProcessorFactory::create($this);
        $this->validators = ValidatorFactory::create($this);
        $this->repository = new Memory;

        // TODO: Необходимо создать нормализовать параметры
        $this->options = new Memory($options);
        $this->cache = new Memory;
    }

    /**
     * @param iterable $source
     * @return ExchangeResultInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotImplementedException
     * @throws ReflectionException
     */
    public function execute(iterable $source): ExchangeResultInterface
    {
        $result = $this->createResult();

        $this->validate($result);
        if (!$result->isSuccess()) {
            return $result;
        }

        $this->beforeRunEvent();

        try {
            if ($this->logger) {
                $this->processor?->setLogger($this->logger);
            }

            $this->processor->run($source, $result);
        } catch (Throwable $e) {
            $this->handleException($e, $result);
        }

        $this->afterRunEvent();

        return $result;
    }

    /**
     * Получение хэша импорта
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHash(): string
    {
        return (string)$this->getOptions()->get('hash', '');
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
     * @throws NotImplementedException
     * @throws ReflectionException
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
     * @throws NotImplementedException
     * @throws ReflectionException
     */
    private function afterRunEvent(): void
    {
        $this->dispatch(new Events\Event(ExchangeEvent::AfterRun->value));
        $this->repository->set('date_up', 0);
    }
}
