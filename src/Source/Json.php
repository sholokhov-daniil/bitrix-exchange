<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;
use ArrayIterator;
use Sholokhov\BitrixExchange\Helper\Helper;

/**
 * Источник данных на основе json строки
 *
 * @package Source
 * @since 1.0.0
 * @version 1.0.0
 */
class Json implements Iterator
{
    /**
     * JSON строка
     *
     * @var string
     * @version 1.0.0
     * @since 1.0.0
     */
    private readonly string $json;

    /**
     * Конфигурация источника данных
     *
     * @var array
     * @version 1.0.0
     * @since 1.0.0
     */
    private readonly array $options;

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
     * @param array $options Конфигурация источника
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $json, array $options = [])
    {
        $this->json = $json;
        $this->options = $options;
    }

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
     * Значение является можественным
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isMultiple(): bool
    {
        return (bool)$this->options['multiple'];
    }

    private function getIterator(): Iterator
    {
        return $this->iterator ??= $this->loadIterator();
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
        return $this->isMultiple() && is_array($data) ? new ArrayIterator($data) : new ArrayIterator([$data]);
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

        if (!is_array($data)) {
            return null;
        }


        $sourceKey = $this->getSourceKey();

        return $sourceKey ? Helper::getArrValueByPath($data, $sourceKey) : $data;
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
        return $this->getIterator()->key();
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
     * Ключ в котором хранятся данные источника
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getSourceKey(): string
    {
        return (string)($this->options['source_key'] ?? '');
    }
}