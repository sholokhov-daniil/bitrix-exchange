<?php

namespace Sholokhov\Exchange\Context;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Sholokhov\Exchange\Processor\ProcessorInterface;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Validators\ValidatorInterface;

final class ExchangeContext implements ExchangeContextInterface
{
    public function __construct(
        private readonly Closure $processorFactory,
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ContainerInterface $options,
        private readonly RepositoryInterface $repository
    )
    {

    }

    public function getProcessorFactory(): Closure
    {
        return $this->processorFactory;
    }

    public function getValidatorFactory(): Closure
    {
        return $this->validator;
    }

    public function getEventDispatcherFactory(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getOptions(): ContainerInterface
    {
        return $this->options;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository
    }
}