<?php

namespace Sholokhov\Exchange\Cache;

use DateInterval;

use Bitrix\Main\Data\Cache;
use Sholokhov\Exchange\Helper\Helper;

/**
 * Менеджер управления кэшем модуля.
 *
 * Объект реализован по шаблону "Делегирование"
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class CacheManager extends AbstractCache implements TaggedCacheAwareInterface
{
    use TaggedCacheAwareTrait;

    /**
     * Полный путь размещения кеша.
     *
     * @var string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected string $path;

    /**
     * Время кеширования.
     *
     * @var int
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected int $ttl = 36000;

    /**
     * Механизм кеширования
     *
     * @var Cache
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected Cache $cache;

    public function __construct(string $group = 'general')
    {
        $this->cache = Cache::createInstance();
        $this->path = Helper::getModuleID() . DIRECTORY_SEPARATOR . $group;
    }

    /**
     * Проверка наличия кеша
     *
     * @param string $key
     * @return bool
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function has(string $key): bool
    {
        return $this->cache->initCache($this->getTtl(), $key, $this->getPath());
    }

    /**
     * Положить данные в кэш.
     *
     * @param string $key
     * @param mixed $value
     * @param int|DateInterval|null $ttl
     * @return mixed
     *
     * @throws \DateInvalidOperationException
     * @since 1.2.0
     * @version 1.2.0
     */
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $result = false;

        if (null !== $ttl) {
            $this->setTtl($ttl);
        }

        if ($this->cache->startDataCache($this->getTtl(), $key, $this->getPath())) {
            $this->taggedCache?->start($this->getPath());
            $this->taggedCache?->end();
            $this->cache->endDataCache($value);
            $result = true;
        }

        return $result;
    }

    /**
     * Получение данных их кэша.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->initCache($this->getTtl(), $key, $this->getPath())
            ? $this->cache->getVars()
            : $default;
    }

    /**
     * Очистка кэша по ключу.
     *
     * @param string $key
     * @return bool
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function delete(string $key): bool
    {
        $this->cache->clean($key);
        return true;
    }

    /**
     * Очистить кэша.
     *
     * @return bool
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function clear(): bool
    {
        $this->cache->cleanDir($this->getPath());
        $this->taggedCache?->clear();

        return true;
    }

    /**
     * Логика установки тегированного кеша
     *
     * @param int $ttl
     * @return AbstractCache
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected function logicSetTtl(int $ttl): AbstractCache
    {
        $this->ttl = $ttl;
        return $this;
    }
    /**
     * Получить время кэширования
     *
     * @return int
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * Получение пути хранения кэша
     *
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getPath(): string
    {
        return $this->path;
    }
}