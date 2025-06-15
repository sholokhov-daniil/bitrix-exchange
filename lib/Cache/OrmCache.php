<?php

namespace Sholokhov\Exchange\Cache;

use DateInterval;

use Bitrix\Main\Application;
use Bitrix\Main\DB\ArrayResult;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\SystemException;

/**
 * Управляем кешем ORM сущности
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class OrmCache extends AbstractCache
{
    /**
     * Сущность, для которой организовано кеширования
     *
     * @var Entity
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected readonly Entity $entity;

    /**
     * Время кеширования
     *
     * @var int
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    protected int $ttl = 36000;

    /**
     * @param Entity $entity
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
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
        return !empty($this->entity->readFromCache($this->getTtl(), $key));
    }

    /**
     * Положить данные в кэш
     *
     * @param string $key
     * @param mixed $value
     * @param int|DateInterval|null $ttl
     * @return mixed
     * @throws SystemException
     * @throws ObjectPropertyException
     * @throws \DateInvalidOperationException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        if (null !== $ttl) {
            $this->setTtl($ttl);
        }


        $this->entity->writeToCache(new ArrayResult($value), $key);

        return true;
    }

    /**
     * Очистка кеша
     *
     * @return bool
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function clear(): bool
    {
        $this->entity->cleanCache();
        return true;
    }


    /**
     * Получение закешированных данных
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
        return $this->entity->readFromCache($this->getTtl(), $key)->fetchAll();
    }

    /**
     * Очистка кеша
     *
     * @param string $key
     * @return bool
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function delete(string $key): bool
    {
        $cache = Application::getInstance()->getManagedCache();
        $cache->clean($key, 'orm_' . $this->entity->getDataClass()::getTableName());
        return true;
    }

    /**
     * Получить время кеширования данных
     *
     * @return int
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTtl(): int
    {
        return $this->entity->getCacheTtl($this->ttl);
    }

    /**
     * Установка время кеширования данных
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
        $this->entity->getCacheTtl($this->ttl);

        return $this;
    }
}