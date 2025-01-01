<?php

namespace Sholokhov\Exchange\Source;

use Iterator;
use ArrayIterator;

/**
 * Источник данных на основе json строки
 *
 * @internal Наследуемся на свой страх и риск
 */
class Json extends AbstractSource
{
    private bool $multiple = false;

    /**
     * @param string $json JSON строка
     * @param string|int|null $sourceKey Ключ из которого необходимо брать данные. Если не указать, что подгружаются все данные
     */
    public function __construct(
        private readonly string $json,
        private readonly string|int|null $sourceKey = null,
    )
    {

    }

    /**
     * Значение является можественным
     * @return bool
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
     */
    protected function load(): Iterator
    {
        $data = $this->loadData();

        if ($this->isMultiple() && is_array($data)) {
            return new ArrayIterator($data);
        } else {
            return new Item($data);
        }
    }

    /**
     * Загрузка данных из json файла
     *
     * @return mixed
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
}