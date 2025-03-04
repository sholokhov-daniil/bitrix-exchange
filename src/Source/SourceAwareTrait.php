<?php

namespace Sholokhov\Exchange\Source;

/**
 * @implements  SourceAwareInterface
 */
trait SourceAwareTrait
{
    protected ?SourceInterface $source = null;

    /**
     * Указание источника данных
     *
     * @param SourceInterface $source
     * @return static
     */
    public function setSource(SourceInterface $source): self
    {
        $this->source = $source;
        return $this;
    }
}