<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Fields\Field;

interface Options
{
    /**
     * Карта обмена
     *
     * @return Field[]
     */
    public function getMap(): array;

    /**
     * Внешний ключ.
     * Используется для идентификации элементов во время импорта.
     *
     * @return string
     */
    public function getForeignKey(): string;
}