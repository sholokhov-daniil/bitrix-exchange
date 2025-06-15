<?php

namespace Sholokhov\Exchange\Cache;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
class TaggedCache implements TaggedCacheInterface
{
    /**
     * @var \Bitrix\Main\Data\TaggedCache
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected \Bitrix\Main\Data\TaggedCache $taggedCache;

    /**
     * @param string $tag
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function __construct(protected string $tag)
    {
        $this->taggedCache = Application::getInstance()->getTaggedCache();
    }

    /**
     * Старт кэширования.
     *
     * @param string $path
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function start(string $path): void
    {
        $this->taggedCache->startTagCache($path);
        $this->taggedCache->registerTag($this->tag);
    }

    /**
     * Остановка кэширования.
     *
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function end(): void
    {
        $this->taggedCache->endTagCache();
    }

    /**
     * Очистка кэша.
     *
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function clear(): void
    {
        $this->taggedCache->clearByTag($this->tag);
    }
}