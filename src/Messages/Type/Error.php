<?php

namespace Sholokhov\BitrixExchange\Messages\Type;

use Throwable;

/**
 * @deprecated Будет переделано на {@see \Bitrix\Main\Error}
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Error
{
    /**
     * @param string $message
     * @param int $code
     * @param array $context
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(
        protected string $message,
        protected int $code = 0,
        protected array $context = []
    )
    {
    }

    /**
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __toString(): string
    {
        return $this->message;
    }

    /**
     * Создание ошибки на основе исключения
     *
     * @param Throwable $throwable
     * @param array $context
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function createFromThrowable(Throwable $throwable, array $context = [])
    {
        return new static($throwable->getMessage(), $throwable->getCode(), $context);
    }

    /**
     * Получение текста ошибки
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Получение кода ошибки
     *
     * @return int
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Получение контекста ошибки
     *
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getContext(): array
    {
        return $this->context;
    }
}