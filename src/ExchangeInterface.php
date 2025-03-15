<?php

namespace Sholokhov\Exchange;

use Iterator;
use Sholokhov\Exchange\Messages\ResultInterface;

use Psr\Log\LoggerAwareInterface;

interface ExchangeInterface extends LoggerAwareInterface
{
    /**
     * Запуск обмена данными
     *
     * @param Iterator $source
     * @return ResultInterface
     */
    public function execute(Iterator $source): ResultInterface;
}