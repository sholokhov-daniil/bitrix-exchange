<?php

namespace Sholokhov\Exchange\Messages;

use Sholokhov\Exchange\Repository\Result\ResultRepositoryInterface;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
interface ExchangeResultInterface extends ResultInterface
{
    /**
     * Получение результата работы
     *
     * @return ResultRepositoryInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getData(): ?ResultRepositoryInterface;
}