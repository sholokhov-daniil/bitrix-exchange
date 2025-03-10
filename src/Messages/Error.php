<?php

namespace Sholokhov\Exchange\Messages;

use Sholokhov\Exchange\Messages\Message;

/**
 * Структура сообщения об ошибке
 */
interface Error extends Message
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