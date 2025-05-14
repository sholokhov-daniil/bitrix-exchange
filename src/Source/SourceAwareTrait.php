<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;

/**
 * @implements  SourceAwareInterface
 *
 * @since 1.0.0
 * @version 1.0.0
 */
trait SourceAwareTrait
{
    protected ?Iterator $source = null;

    /**
     * Указание источника данных
     *
     * @param Iterator $source
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setSource(Iterator $source): static
    {
        $this->source = $source;
        return $this;
    }
}