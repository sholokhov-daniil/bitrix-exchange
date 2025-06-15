<?php

namespace Sholokhov\Exchange\Cache;

use DateTime;
use DateInterval;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * Основной класс кеширования данных
 *
 * @since 1.2.0
 * @version 1.2.0
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * Логика указания время кеширования
     *
     * @param int $ttl
     * @return self
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    abstract protected function logicSetTtl(int $ttl): self;

    /**
     * Записать значение в кеш посредством вызова обработчика.
     *
     * @param string $key
     * @param callable $callback
     * @param int|DateInterval|null $ttl
     * @return mixed
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setInvoke(string $key, callable $callback, null|int|DateInterval $ttl = null): mixed
    {
        $cacheData = call_user_func($callback);
        $this->set($key, $cacheData, $ttl);

        return $cacheData;
    }

    /**
     * Сохраняет в кеше набор пар ключа => обработчик с необязательным TTL.
     *
     * @param iterable $callbacks
     * @param DateInterval|int|null $ttl
     * @return array
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setInvokeMultiple(iterable $callbacks, DateInterval|int|null $ttl = null): array
    {
        $result = [];

        foreach ($callbacks as $key => $callback) {
            $result[$key] = $this->setInvoke($key, $callback);
        }

        return $result;
    }

    /**
     * Сохраняет в кеше набор пар ключа => значение с необязательным TTL.
     *
     * @param iterable $values
     * @param DateInterval|int|null $ttl
     * @return bool
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $result = true;

        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Получает несколько элементов кэша по их уникальным ключам.
     *
     * @param iterable $keys
     * @param mixed|null $default
     * @return iterable
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * Удаляет несколько элементов кэша за одну операцию.
     *
     * @param string[] $keys
     * @return bool
     * @throws InvalidArgumentException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;

        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Установить время жизни кеша
     *
     * @param int|DateInterval $ttl
     * @return self
     *
     * @throws \DateInvalidOperationException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTtl(int|DateInterval $ttl): self
    {
        if ($ttl instanceof DateInterval) {
            $dieTime = (new DateTime())->sub($ttl);
            $live = (time() / $dieTime->getTimestamp()) / 60;
        } else {
            $live = $ttl;
        }

        return $this->logicSetTtl(max($live, 0));
    }
}