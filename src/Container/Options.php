<?php

namespace Sholokhov\Exchange\Container;

use Sholokhov\Exchange\OptionInterface;
use Sholokhov\Exchange\Fields\FieldInterface;

class Options extends Container implements OptionInterface
{
    /**
     * Карта обмена
     *
     * @return FieldInterface[]
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
     */
    public function getForeignKey(): string
    {
        return (string)$this->get('foreign_key');
    }
}