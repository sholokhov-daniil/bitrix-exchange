<?php

namespace Sholokhov\Exchange\Cache;

use DateInterval;
use Exception;

/**
 * Описание объекта кеша.
 *
 * @since 1.2.0
 * @version 1.2.0
 */
interface CacheInterface extends \Psr\SimpleCache\CacheInterface
{
    /**
     * Записать значение в кеш посредством вызова обработчика.
     *
     * @param string $key
     * @param callable $callback
     * @param int|DateInterval|null $ttl
     * @return mixed
     * @throws Exception
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setInvoke(string $key, callable $callback, null|int|DateInterval $ttl = null): mixed;

    /**
     * Сохраняет в кеше набор пар ключ => обработчик с необязательным TTL.
     *
     * @param iterable $callbacks
     * @param int|DateInterval|null $ttl
     * @return mixed
     * @throws Exception
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setInvokeMultiple(iterable $callbacks, null|int|\DateInterval $ttl = null): mixed;

    /**
     * Получить время кеширования
     *
     * @return int
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTtl(): int;

    /**
     * Установить время кеширования.
     *
     * @param int|DateInterval $ttl
     * @return CacheInterface
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTtl(int|DateInterval $ttl): CacheInterface;
}