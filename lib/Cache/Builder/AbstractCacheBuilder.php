<?php

namespace Sholokhov\Exchange\Cache\Builder;

use Sholokhov\Exchange\Cache\CacheInterface;
use Sholokhov\Exchange\Cache\CacheManager;
use Sholokhov\Exchange\Cache\TaggedCache;

use Bitrix\Main\Context;

/**
 * Базовый фабрика по генерации менеджера кэша.
 *
 * @since 1.2.0
 * @version 1.2.0
 */
abstract class AbstractCacheBuilder
{
    /**
     * @var TaggedCache|null
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected ?TaggedCache $taggedCache = null;

    /**
     * ID сайта, которому относится кеш
     *
     * @var string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected string $siteId;

    /**
     * @param string|null $siteId
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function __construct(string $siteId = null)
    {
        $this->siteId = $siteId ?: (string)Context::getCurrent()->getSite();
    }

    /**
     * Получение группы кэширования.
     *
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    abstract public function getGroup(): string;

    /**
     * Генерация менеджера управления кэшем.
     *
     * @return CacheInterface
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function make(): CacheInterface
    {
        $group = $this->siteId . '/' . $this->getGroup();
        $cache = new CacheManager($group);

        if ($this->taggedCache) {
            $cache->setTaggedCache($this->taggedCache);
        }

        return $cache;
    }

    /**
     * Установка тегированного кэша.
     *
     * @param TaggedCache|null $taggedCache
     * @return $this
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTaggedCache(TaggedCache $taggedCache = null): self
    {
        $this->taggedCache = $taggedCache ?: new TaggedCache($this->getGroup());
        return $this;
    }

    /**
     * Получение ID сайта, на основе которого происходит работа с кэшем.
     *
     * @return string|null
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getSiteID(): ?string
    {
        return $this->siteId;
    }
}