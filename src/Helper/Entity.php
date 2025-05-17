<?php

namespace Sholokhov\BitrixExchange\Helper;

use ReflectionClass;
use ReflectionException;

/**
 * @package Helper
 * @since 1.0.0
 * @version 1.0.0
 */
class Entity
{
    /**
     * Получение атрибута объекта
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getAttribute(string|object $entity, string $attribute): ?object
    {
        return self::getAttributeByReflection(new ReflectionClass($entity), $attribute);
    }

    /**
     * Получение атрибута у текущего объекта или его родителя
     *
     * @param string|object $entity
     * @param string $attribute
     * @return object|null
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getAttributeChain(string|object $entity, string $attribute): ?object
    {
        $reflection = new ReflectionClass($entity);
        $fountAttribute = self::getAttributeByReflection($reflection, $attribute);

        if ($fountAttribute) {
            return $fountAttribute;
        }

        while ($reflection = $reflection->getParentClass()) {
            if ($fountAttribute = self::getAttributeByReflection($reflection, $attribute)) {
                return $fountAttribute;
            }
        }

        return null;
    }

    /**
     * Получение атрибута из описания класса
     *
     * @param ReflectionClass $reflection
     * @param string $attribute
     * @return object|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected static function getAttributeByReflection(ReflectionClass $reflection, string $attribute): ?object
    {
        $attribute = $reflection->getAttributes($attribute)[0] ?? null;
        return $attribute?->newInstance();
    }
}