<?php

namespace Sholokhov\Exchange\Events;

interface EventInterface
{
    public function getType(): string;
    public function getParameters(): array;
}