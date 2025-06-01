<?php

namespace Sholokhov\BitrixExchange\Exception;

use Exception;

/**
 * Основное исключение обмена
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class ExchangeException extends Exception
{
    /**
     * Контекст исключения
     *
     * @var mixed|null
     * @since 1.0.0
     * @version 1.0.0
     */
    private mixed $context = null;

    /**
     * Получение контекста исключения
     *
     * @return mixed
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getContext(): mixed
    {
        return $this->context;
    }

    /**
     * Установка контекста исключения
     *
     * @param mixed $context
     * @return $this
     * @version 1.0.0
     * @since 1.0.0
     */
    public function setContext(mixed $context): ExchangeException
    {
        $this->context = $context;
        return $this;
    }
}