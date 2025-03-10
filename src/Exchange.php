<?php

namespace Sholokhov\Exchange;

use Iterator;
use Sholokhov\Exchange\Messages\Result;

use Psr\Log\LoggerAwareInterface;

interface Exchange extends LoggerAwareInterface
{
    /**
     * Запуск обмена данными
     *
     * @param Iterator $source
     * @return Result
     */
    public function execute(Iterator $source): Result;
}