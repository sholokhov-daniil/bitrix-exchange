<?php

namespace Sholokhov\Exchange\DTO\Source\Xml;

use Sholokhov\Exchange\DTO\Source\SourceItem;
use Sholokhov\Exchange\Source\SourceItemInterface;

class XmlItem extends SourceItem
{
    private array $attributes = [];

    public function setAttributes(array $attributes): self
    {
        $this->attributes = [];
        array_walk($attributes, [$this, 'addAttribute']);
        return $this;
    }

    public function addAttribute(SourceItemInterface $attribute): self
    {
        $this->attributes[$attribute->getCode()] = $attribute;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name): ?SourceItemInterface
    {
        return $this->attributes[$name] ?? null;
    }
}