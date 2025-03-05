<?php

namespace Sholokhov\Exchange\Target\Bitrix;

use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Source\SourceAwareTrait;
use Sholokhov\Exchange\Target\TargetInterface;

/**
 * Добавляет значения в очередь
 */
class QueueTarget implements TargetInterface
{
    use SourceAwareTrait;

    public function execute(): ResultInterface
    {
        return new Result;
    }
}