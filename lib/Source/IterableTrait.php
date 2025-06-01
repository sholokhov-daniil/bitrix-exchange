<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;
use EmptyIterator;

/**
 * @implements Iterator
 *
 * @package Source
 * @since 1.0.0
 * @version 1.0.0
 */
trait IterableTrait
{
    /**
     * @var Iterator|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected ?Iterator $iterator = null;

    /**
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function current(): mixed
    {
        return $this->getIterator()->current();
    }

    /**
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function next(): void
    {
        $this->getIterator()->next();
    }

    /**
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function key(): mixed
    {
        return $this->getIterator()->current();
    }

    /**
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function valid(): bool
    {
        return $this->getIterator()->valid();
    }

    /**
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function rewind(): void
    {
        $this->getIterator()->rewind();
    }

    /**
     * Получение итератора данных
     *
     * @return Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getIterator(): Iterator
    {
        return $this->iterator ??= $this->load();
    }

    /**
     * Инициализация итератора данных
     *
     * @return Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function load(): Iterator
    {
        return new EmptyIterator;
    }
}