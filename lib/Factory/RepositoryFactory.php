<?php

namespace Sholokhov\Exchange\Factory;

use Exception;
use ReflectionException;
use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Target\Attributes\CacheContainer;
use Sholokhov\Exchange\Target\Attributes\OptionsContainer;

class RepositoryFactory
{
    /**
     * Инициализация объекта конфигураций на основе атрибута {@see OptionsContainer}
     *
     * @param object $exchange
     * @param array $data
     * @return RepositoryInterface
     * @throws ReflectionException
     */
    public static function createOptions(object $exchange, array $data): RepositoryInterface
    {
        /** @var OptionsContainer $attribute */
        $attribute = Entity::getAttributeChain($exchange, OptionsContainer::class);
        $entity = $attribute->getEntity();

        if (!is_subclass_of($entity, RepositoryInterface::class)) {
            throw new Exception('The exchange configuration repository is not a subclass of ' . RepositoryInterface::class);
        }

        return new $entity($data);
    }

    /**
     * Инициализация объекта кеширования
     *
     * @param object $exchange
     * @param array $configuration
     * @return RepositoryInterface
     * @throws ReflectionException
     */
    public static function createCache(object $exchange, array $configuration = []): RepositoryInterface
    {
        /** @var CacheContainer $attribute */
        $attribute = Entity::getAttribute($exchange, CacheContainer::class) ?: Entity::getAttribute(self::class, CacheContainer::class);
        $entity = $attribute->getEntity();

        if (!is_subclass_of($entity, RepositoryInterface::class)) {
            throw new Exception('The exchange cache repository is not a subclass of ' . RepositoryInterface::class);
        }

        return new $entity($configuration);
    }
}