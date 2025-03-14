<?php

namespace Sholokhov\Exchange\Target;

use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\AddResult;
use Sholokhov\Exchange\Messages\Type\DataResult;

/**
 * Добавляет значения в очередь
 */
class QueueTarget extends AbstractExchange
{

    protected function add(array $item): AddResult
    {
        return new AddResult;
    }

    protected function update(array $item): Result
    {
        return new DataResult;
    }

    protected function exists(array $item): bool
    {
        return false;
    }
}