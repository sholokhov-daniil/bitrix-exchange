<?php

namespace Sholokhov\Exchange\Target;

use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;

/**
 * Добавляет значения в очередь
 */
class QueueTarget extends AbstractExchange
{

    protected function add(array $item): ResultInterface
    {
        return new DataResult;
    }

    protected function update(array $item): ResultInterface
    {
        return new DataResult;
    }

    protected function exists(array $item): bool
    {
        return false;
    }
}