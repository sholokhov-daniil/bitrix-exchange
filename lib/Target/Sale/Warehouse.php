<?php

namespace Sholokhov\Exchange\Target\Sale;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;

class Warehouse extends Exchange
{

    protected function add(array $item): DataResultInterface
    {
        // TODO: Implement add() method.
    }

    protected function update(array $item): DataResultInterface
    {
        // TODO: Implement update() method.
    }

    protected function exists(array $item): bool
    {
        // TODO: Implement exists() method.
    }

    protected function isMultipleField(FieldInterface $field): bool
    {
        // TODO: Implement isMultipleField() method.
    }
}