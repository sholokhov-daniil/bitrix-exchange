<?php

namespace Sholokhov\BitrixExchange\Exception\Target;

use Sholokhov\BitrixExchange\Exception\ExchangeException;
use Throwable;

/**
 * Исключение означающее принудительную остановку обмена элемента.
 *
 * Как правило, исключение используется в событиях
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class ExchangeItemStoppedException extends ExchangeException
{
    /**
     * @param string $message
     * @param Throwable|null $throwable
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $message = "", Throwable $throwable = null)
    {
        parent::__construct($message, 444, $throwable);
    }
}