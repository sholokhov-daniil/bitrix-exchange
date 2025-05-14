<?php

namespace Sholokhov\BitrixExchange\Repository\Target;

use Sholokhov\BitrixExchange\Repository\Types\Memory;
use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
class Options extends Memory
{
    /**
     * Карта обмена
     *
     * @return FieldInterface[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getMap(): array
    {
        return (array)$this->get('map');
    }

    /**
     * Внешний ключ.
     * Используется для идентификации элементов во время импорта.
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getForeignKey(): string
    {
        return (string)$this->get('foreign_key');
    }
}