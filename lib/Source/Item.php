<?php

namespace Sholokhov\Exchange\Source;

use ArrayIterator;
use Bitrix\Main\Diag\Debug;
use Iterator;

/**
 * Источник данных по одному элементу
 *
 * @internal Наследуемся на свой страх и риск
 */
class Item extends AbstractSource
{
    public function __construct(private mixed $item)
    {
    }

    /**
     * Загрузка итератора
     *
     * @return Iterator
     */
    protected function load(): Iterator
    {
        $iterator = new ArrayIterator([$this->item]);
        $this->item = null;

        return $iterator;
    }
}