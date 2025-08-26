<?php

namespace Sholokhov\Exchange\Context;

use Closure;
use Sholokhov\Exchange\Processor\ProcessorInterface;
use Sholokhov\Exchange\Validators\ValidatorInterface;
use Sholokhov\Exchange\Repository\RepositoryInterface;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

interface ExchangeContextInterface
{
    /**
     * Получение порядка действий при обмене
     *
     * @return Closure
     */
    public function getProcessorFactory(): Closure;

    /**
     * Производит валидацию данных, для возможности запуска обмена
     *
     * @return ValidatorInterface
     */
    public function getValidatorFactory(): Closure;

    /**
     * Получение диспетчера сообщений
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcherFactory(): Closure;

    /**
     * Конфигурация обмена
     *
     * @return ContainerInterface
     */
    public function getOptions(): ContainerInterface;

    /**
     * Хранилище информации обмена
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;
}