<?php

namespace Sholokhov\Exchange\Cache;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
trait CacheAwareTrait
{
    /**
     * @var CacheInterface|null
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected ?CacheInterface $cache = null;

    /**
     * @param CacheInterface $cache
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}