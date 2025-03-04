<?php

namespace Sholokhov\Exchange\Source;

use ArrayIterator;

/**
 * Источник данных по одному элементу
 *
 * @internal Наследуемся на свой страх и риск
 */
class Item implements SourceInterface
{
    private ArrayIterator $iterator;

    public function __construct(mixed $item)
    {
        $this->iterator = new ArrayIterator([$item]);
    }

    public function fetch(): mixed
    {
        if (!$this->iterator->valid()) {
            return null;
        }

        $current = $this->iterator->current();
        $this->iterator->next();

        return $current;
    }
}