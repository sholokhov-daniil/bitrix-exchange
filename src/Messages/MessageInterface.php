<?php

namespace Sholokhov\Exchange\Messages;

use Stringable;

interface MessageInterface extends Stringable
{
    /**
     * Тело ошибки
     *
     * @return string
     */
    public function getMessage(): string;
}