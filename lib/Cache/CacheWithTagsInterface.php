<?php

namespace Sholokhov\Exchange\Cache;

/**
 * Кеширование с поддержкой тегов
 *
 * @since 1.2.0
 * @version 1.2.0
 */
interface CacheWithTagsInterface extends CacheInterface, TaggedCacheAwareInterface
{
}