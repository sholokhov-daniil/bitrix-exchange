<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Messages\ExchangeResultInterface;

use Psr\Log\LoggerAwareInterface;

/**
 * @since 1.0.0
 * @version 1.1.0
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

    /**
     * Получение хэша обмена
     *
     * @return string
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function getHash(): string;
}