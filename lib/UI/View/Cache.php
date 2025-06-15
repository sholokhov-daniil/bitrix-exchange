<?php

namespace Sholokhov\Exchange\UI\View;

use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Cache\CacheInterface;

use Twig\Cache\CacheInterface as TwigCacheInterface;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * Механизм кеширования данных Twig
 *
 * @since 1.2.0
 * @version 1.2.0
 */
readonly class Cache implements TwigCacheInterface
{
    public function __construct(private CacheInterface $cache)
    {
    }

    /**
     * Генерация ключа кэша
     *
     * @param string $name
     * @param string $className
     * @return string
     *
     * @since 1.2.0
     */
    public function generateKey(string $name, string $className): string
    {
        return hash(PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $className);
    }

    /**
     * Создание хеш записи
     *
     * @param string $key
     * @param string $content
     * @return void
     *
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function write(string $key, string $content): void
    {
        $this->cache->set($key, $content);
        $this->cache->set($this->generateTimestampHash($key), time());
    }

    /**
     * Загрузка хеша
     *
     * @param string $key
     * @return void
     *
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function load(string $key): void
    {
        if ($this->cache->has($key)) {
            $content = $this->cache->get($key);

            eval(mb_substr($content, 5));
        }
    }

    /**
     * Получить время последнего изменения
     *
     * @param string $key
     * @return int
     *
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTimestamp(string $key): int
    {
        $hash = $this->generateTimestampHash($key);
        return $this->cache->has($hash) ? (int)$this->cache->get($hash) : 0;
    }

    /**
     * Генерация timestamp хеша
     *
     * @param string $key
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected function generateTimestampHash(string $key): string
    {
        return sprintf('%s_%s', $key, Entity::getCode($this));
    }
}