<?php

namespace Sholokhov\Exchange\Target;

use Bitrix\Main\Diag\Debug;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

/**
 * Добавляет значения в очередь
 */
class QueueTarget extends AbstractExchange
{

    protected function add(array $item): Result
    {
        Debug::dump($item);

        return new DataResult;
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