<?php

namespace Sholokhov\BitrixExchange\Target\Attributes;

use Attribute;
use Sholokhov\BitrixExchange\Repository\Types\Memory;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_CLASS)]
class CacheContainer
{
    /**
     * @param string $entity
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(private readonly string $entity = Memory::class)
    {
    }

    /**
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getEntity(): string
    {
        return $this->entity;
    }
}