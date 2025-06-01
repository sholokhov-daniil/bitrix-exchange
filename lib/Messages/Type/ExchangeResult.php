<?php

namespace Sholokhov\Exchange\Messages\Type;

use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Repository\Result\ResultRepositoryInterface;

/**
 * Результат выполненных действий
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class ExchangeResult extends Result implements ExchangeResultInterface
{
    public function __construct(private readonly ?ResultRepositoryInterface $repository = null)
    {
    }

    /**
     * Получение хранилища результата работы действия
     *
     * @return ResultRepositoryInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getData(): ?ResultRepositoryInterface
    {
        return $this->repository;
    }
}