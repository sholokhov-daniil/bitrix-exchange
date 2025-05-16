<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;
use ArrayIterator;

/**
 * Источник данных на основе сериализованной строки
 *
 * @internal Наследуемся на свой страх и риск
 *
 * @package Source
 * @since 1.0.0
 * @version 1.0.0
 */
class SerializeItem implements Iterator
{
    private Iterator $iterator;

    /**
     * @param string $data Строка с данными
     * @param bool $multiple Данные являются множественными
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(
        private readonly string $data,
        private readonly bool $multiple = true,
    )
    {
    }

    /**
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function fetch(): mixed
    {
        $this->iterator ??= $this->load();
        return $this->iterator->fetch();
    }

    /**
     * Инициализация итератора данных из сериализованной строки
     *
     * @return Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function load(): Iterator
    {
        $data = unserialize($this->data);
        return $this->multiple && is_array($data) ? new ArrayIterator($data) : new ArrayIterator([$data]);
    }

    /**
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function current(): mixed
    {
        if (!isset($this->iterator)) {
            $this->iterator = $this->load();
        }

        return $this->iterator->current();
    }

    /**
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function next(): void
    {
        $this->iterator->next();
    }

    /**
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    /**
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }
}