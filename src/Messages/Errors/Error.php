<?php

namespace Sholokhov\Exchange\Messages\Errors;

use Throwable;
use JsonSerializable;

class Error implements \Sholokhov\Exchange\Messages\Error, JsonSerializable
{
    /**
     * @param string $message Описание ошибки
     * @param string $code Код ошибки
     * @param array $context Контекст ошибки (дополнительная информация)
     */
    public function __construct(
        protected string $message,
        protected string $code = '',
        protected array $context = []
    )
    {
    }

    /**
     * Преобразование ошибки в строку
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getMessage(), $this->getCode());
    }

    /**
     * Получить код ошибки
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Описание ошибки
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Контекст ошибки (дополнительная информация)
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Формат данных при Сериализации
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->getContext(),
        ];
    }

    /**
     * Инициализация на основе исключения
     *
     * @param Throwable $throwable
     * @return static
     */
    public static function createFromThrowable(Throwable $throwable): static
    {
        return new static($throwable->getMessage(), $throwable->getCode());
    }
}