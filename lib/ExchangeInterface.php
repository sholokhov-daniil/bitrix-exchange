<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;

use Psr\Log\LoggerAwareInterface;
use Psr\Container\ContainerInterface;

interface ExchangeInterface extends LoggerAwareInterface
{
    /**
     * Запуск обмена данными
     *
     * @param iterable $source
     * @return ExchangeResultInterface
     */
    public function execute(iterable $source): ExchangeResultInterface;

    /**
     * Получение хэша обмена
     *
     * @return string
     */
    public function getHash(): string;

    /**
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     */
    public function isMultipleField(FieldInterface $field): bool;

    /**
     * Получение конфигурации обмена
     *
     * @return ContainerInterface
     */
    public function getOptions(): ContainerInterface;
}