<?php

namespace Sholokhov\BitrixExchange;

use Sholokhov\BitrixExchange\Messages\ExchangeResultInterface;

use Psr\Log\LoggerAwareInterface;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
interface ExchangeInterface extends LoggerAwareInterface
{
    /**
     * Запуск обмена данными
     *
     * @param iterable $source
     * @return ExchangeResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function execute(iterable $source): ExchangeResultInterface;
}