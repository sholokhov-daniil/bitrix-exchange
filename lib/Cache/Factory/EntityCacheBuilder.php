<?php

namespace Sholokhov\Exchange\Cache\Factory;

use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Cache\CacheInterface;

/**
 * Формирование кеша на основе объекта
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class EntityCacheBuilder extends AbstractCacheBuilder
{
    /**
     * Сущность, для которой необходимо сформировать кеш
     *
     * @var string
     * @author Daniil S.
     */
    private string $entity;

    /**
     * @param string|object $entity Объект на основе которого происходит формирование кеша
     * @param string|null $siteId ID сайта, которому принадлежит кеш
     */
    public function __construct(string|object $entity, string|null $siteId = null)
    {
        parent::__construct($siteId);
        $this->entity = is_object($entity) ? $entity::class : $entity;
    }

    /**
     * Получение ключа хранения кэша
     *
     * @return string
     * @author Daniil S.
     */
    public function getGroup(): string
    {
        return Entity::getCode($this->entity);
    }

    /**
     * Создание менеджера кэширования.
     *
     * @param string $entity
     * @param string|null $siteID
     * @return CacheInterface
     * @author Daniil S.
     */
    public static function build(string $entity, string $siteID = null): CacheInterface
    {
        return (new static($entity, $siteID))
            ->make();
    }
}