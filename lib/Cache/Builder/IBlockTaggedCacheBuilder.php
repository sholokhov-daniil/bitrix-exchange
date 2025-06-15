<?php

namespace Sholokhov\Exchange\Cache\Builder;

use Sholokhov\Exchange\Cache\TaggedCache;
use Sholokhov\Exchange\Cache\TaggedCacheInterface;

class IBlockTaggedCacheBuilder
{
    /**
     * @param int $iiBlockId
     * @return TaggedCacheInterface
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function make(int $iiBlockId): TaggedCacheInterface
    {
        return new TaggedCache('iblock_id_' . $iiBlockId);
    }
}