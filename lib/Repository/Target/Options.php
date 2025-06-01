<?php

namespace Sholokhov\Exchange\Repository\Target;

use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Fields\FieldInterface;

/**
 * @since 1.0.0
 * @version 1.0.0
 *
 * @package Repository
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