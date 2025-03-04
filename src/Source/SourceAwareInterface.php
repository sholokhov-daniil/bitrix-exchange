<?php

namespace Sholokhov\Exchange\Source;

interface SourceAwareInterface
{
    /**
     * Указание источника данных
     *
     * @param SourceInterface $source
     * @return self
     */
    public function setSource(SourceInterface $source): self;
}