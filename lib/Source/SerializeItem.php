<?php

namespace Sholokhov\Exchange\Source;

use ArrayIterator;
use Iterator;

/**
 * Источник данных на основе сериализованной строки
 *
 * @internal Наследуемся на свой страх и риск
 */
class SerializeItem extends AbstractSource
{
    /**
     * @param string $data Строка с данными
     * @param bool $multiple Данные являются множественными
     */
    public function __construct(
        private readonly string $data,
        private readonly bool $multiple = true,
    )
    {
    }

    /**
     * Инициализация итератора данных из сериализованной строки
     *
     * @return Iterator
     */
    protected function load(): Iterator
    {
        $data = unserialize($this->data);

        if (($this->multiple && !is_array($data)) || !$this->multiple) {
            return new Item($data);
        }

        return new ArrayIterator($data);
    }
}