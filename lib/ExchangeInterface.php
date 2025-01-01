<?php

namespace Sholokhov\Exchange;

use Bitrix\Main\Result;
use Psr\Log\LoggerAwareInterface;

interface ExchangeInterface extends LoggerAwareInterface
{
    /**
     * Запуск обмена данными
     *
     * @return Result
     */
    public function run(): Result;
}