<?php

namespace Sholokhov\BitrixExchange\Messages\Type;

use Throwable;

use Sholokhov\BitrixExchange\Messages\ErrorInterface;

/**
 * Описание ошибки
 *
 * @version 1.0.0
 * @since 1.0.0
 */
class Error implements ErrorInterface
{
    public function __construct(
        private readonly string $message,
        private readonly int $code = 500,
        private readonly mixed $context = null
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('[%s] %s', $this->getCode(), $this->getMessage());
    }

    /**
     * Создать объект на основе исключения
     *
     * @param Throwable $throwable
     * @return static
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public static function createFromThrowable(Throwable $throwable): static
    {
        return new static($throwable->getMessage(), $throwable->getCode());
    }

    /**
     * Получение кода ошибки
     *
     * @return int
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Текстовое сообщение ошибки
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Получение контекста ошибки
     *
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getContext(): mixed
    {
        return $this->context;
    }
}