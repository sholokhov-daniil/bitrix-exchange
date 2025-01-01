<?php

namespace Sholokhov\Exchange\Source;

use ArrayIterator;

/**
 * Источник данных по массиву элементов
 *
 * @internal Наследуемся на свой страх и риск
 */
class Items implements SourceInterface
{
    private ArrayIterator $iterator;

    public function __construct(array $item)
    {
        $this->iterator = new ArrayIterator($item);
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