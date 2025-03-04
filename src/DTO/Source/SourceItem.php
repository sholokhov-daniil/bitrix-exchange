<?php

namespace Sholokhov\Exchange\DTO\Source;

use Sholokhov\Exchange\Source\SourceItemInterface;

class SourceItem implements SourceItemInterface
{
    private string $code = '';

    private mixed $value = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }
}