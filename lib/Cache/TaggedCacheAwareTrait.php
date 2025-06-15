<?php

namespace Sholokhov\Exchange\Cache;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
trait TaggedCacheAwareTrait
{
    /**
     * Тегированный кеш
     *
     * @var TaggedCacheInterface|null
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected ?TaggedCacheInterface $taggedCache = null;

    /**
     * Установка тегированного кэша
     *
     * @param TaggedCacheInterface|null $cache
     * @return $this
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTaggedCache(TaggedCacheInterface $cache = null): self
    {
        $this->taggedCache = $cache;
        return $this;
    }
}