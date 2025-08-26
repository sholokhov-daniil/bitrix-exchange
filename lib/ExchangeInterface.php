<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;

use Psr\Log\LoggerAwareInterface;

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
     * Проверка наличия элемента сущности
     *
     * @param array $item
     * @return bool
     */
    public function exists(array $item): bool;

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     */
    public function update(array $item): DataResultInterface;

    /**
     * Добавление нового элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     */
    public function add(array $item): DataResultInterface;

    /**
     * Деактивация элементов сущности, которые не пришли в обмене
     *
     * @return void
     */
    public function deactivate(): void;

    /**
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     */
    public function isMultipleField(FieldInterface $field): bool;

    /**
     * Получение хэша обмена
     *
     * @return string
     */
    public function getHash(): string;
}