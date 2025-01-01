<?php

namespace Sholokhov\Exchange\Source;

use Iterator;

/**
 * Источник данных на csv файла
 *
 * @internal Наследуемся на свой страх и риск
 */
class Csv implements Iterator
{
    /** @var resource|null  */
    private $resource = null;

    public function __construct(
        private readonly string $path,
        private readonly string $encoding = 'UTF-8',
    )
    {
        $this->resource = fopen($this->path, 'r');
    }

    public function __destruct()
    {
        fclose($this->resource);
    }

    public function current(): mixed
    {
        // TODO: Implement current() method.
    }

    public function next(): void
    {
        // TODO: Implement next() method.
    }

    public function key(): mixed
    {
        // TODO: Implement key() method.
    }

    public function valid(): bool
    {
        // TODO: Implement valid() method.
    }

    public function rewind(): void
    {
        // TODO: Implement rewind() method.
    }
}