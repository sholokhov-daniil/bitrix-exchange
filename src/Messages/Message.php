<?php

namespace Sholokhov\Exchange\Messages;

use Stringable;

interface Message extends Stringable
{
    /**
     * Тело ошибки
     *
     * @return string
     */
    public function getMessage(): string;
}