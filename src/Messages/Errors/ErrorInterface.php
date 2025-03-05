<?php

namespace Sholokhov\Exchange\Messages\Errors;

use Sholokhov\Exchange\Messages\MessageInterface;

/**
 * Структура сообщения об ошибке
 */
interface ErrorInterface extends MessageInterface
{
    /**
     * Код ошибки
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Контекст ошибки
     *
     * @return array
     */
    public function getContext(): array;
}