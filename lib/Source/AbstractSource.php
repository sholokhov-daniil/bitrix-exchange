<?php

namespace Sholokhov\Exchange\Source;

use Iterator;
use IteratorAggregate;

/**
 * @internal Наследуемся на свой страх и риск
 */
abstract class AbstractSource implements Iterator, IteratorAggregate
{
    private Iterator $iterator;

    /**
     * Загрузка данных, для возможности перебора
     *
     * @return Iterator
     */
    abstract protected function load(): Iterator;

    /**
     * Значение на которое указывает картека
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return $this->getIterator()->current();
    }

    /**
     * Переместить каретку на следующий элемент очереди
     *
     * @return void
     */
    public function next(): void
    {
        $this->getIterator()->next();
    }

    /**
     * Ключ на которую указывает каретка
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return $this->getIterator()->key();
    }

    /**
     * Проверка валидности указания каретки
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->getIterator()->valid();
    }

    /**
     * Переводит каретку в начало очереди
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->getIterator()->rewind();
    }

    /**
     * Инициализация итератора данных
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->iterator ??= $this->load();
    }
}