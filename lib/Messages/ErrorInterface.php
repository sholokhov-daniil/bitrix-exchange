<?php

namespace Sholokhov\BitrixExchange\Messages;

use Stringable;

/**
 * Описание ошибки
 *
 * @version 1.0.0
 * @since 1.0.0
 */
interface ErrorInterface extends Stringable
{
    /**
     * Получение кода ошибки
     *
     * @return int
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getCode(): int;

    /**
     * Текстовое сообщение ошибки
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getMessage(): string;

    /**
     * Получение контекста ошибки
     *
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getContext(): mixed;
}