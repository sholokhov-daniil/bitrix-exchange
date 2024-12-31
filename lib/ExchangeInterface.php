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

    /**
     * ID сайта которому принадлежит обмен данными
     *
     * @return string
     */
    public function getSiteID(): string;
}