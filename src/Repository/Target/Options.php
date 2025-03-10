<?php

namespace Sholokhov\Exchange\Repository\Target;

use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Options;
use Sholokhov\Exchange\Fields\Field;

class Options extends Memory implements Options
{
    /**
     * Карта обмена
     *
     * @return Field[]
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