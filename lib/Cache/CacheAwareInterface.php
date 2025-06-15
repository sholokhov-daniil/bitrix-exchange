<?php

namespace Sholokhov\Exchange\Cache;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
interface CacheAwareInterface
{
    /**
     * @param CacheInterface $cache
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setCache(CacheInterface $cache): void;
}