<?php

namespace Sholokhov\Exchange\Cache;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
interface TaggedCacheAwareInterface
{
    /**
     * Установка тэгированного кэша
     *
     * @param TaggedCacheInterface $cache
     * @return self
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTaggedCache(TaggedCacheInterface $cache): self;
}