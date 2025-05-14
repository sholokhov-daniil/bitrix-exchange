<?php

namespace Sholokhov\BitrixExchange;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
interface OptionsInterface
{
    /**
     * Карта обмена
     *
     * @return FieldInterface[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getMap(): array;

    /**
     * Внешний ключ.
     * Используется для идентификации элементов во время импорта.
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getForeignKey(): string;
}