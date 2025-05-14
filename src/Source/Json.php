<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;
use ArrayIterator;

/**
 * Источник данных на основе json строки
 *
 * @internal Наследуемся на свой страх и риск
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Json implements Iterator
{
    /**
     * JSON хранит множественное значение
     *
     * @var bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private bool $multiple = false;

    /**
     * @var Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private Iterator $iterator;

    /**
     * @param string $json JSON строка
     * @param string|int|null $sourceKey Ключ из которого необходимо брать данные. Если не указать, что подгружаются все данные
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(
        private readonly string $json,
        private readonly string|int|null $sourceKey = null,
    )
    {
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
            $this->iterator = $this->loadIterator();
        }

        return $this->iterator->current();
    }


    /**
     * Значение является можественным
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Указание формата данных (множественное или один элемент)
     *
     * @param bool $multiple
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setMultiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Загрузка данных
     *
     * @return Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function loadIterator(): Iterator
    {
        $data = $this->loadData();
        return $this->multiple && is_array($data) ? new ArrayIterator($data) : new ArrayIterator([$data]);
    }

    /**
     * Загрузка данных из json файла
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function loadData(): mixed
    {
        if (!json_validate($this->json)) {
            return null;
        }

        $data = json_decode($this->json, true);

        if ($this->sourceKey !== null) {
            if (is_array($data)) {
                $data = $data[$this->sourceKey];
            } else {
                $data = null;
            }
        }

        return $data;
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