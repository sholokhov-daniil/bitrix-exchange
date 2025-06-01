<?php

namespace Sholokhov\BitrixExchange\Events\Factory;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

use Sholokhov\BitrixExchange\Helper\Entity;
use Sholokhov\BitrixExchange\Events\Event;
use Sholokhov\BitrixExchange\Events\EventInterface;
use Sholokhov\BitrixExchange\Target\Attributes\Event as Attribute;

/**
 * Создает объекты события, которые зарегистрированы в объекте посредством атрибутов
 *
 * @package Event
 * @since 1.0.0
 * @version 1.0.0
 */
readonly class AttributeEventFactory
{
    /**
     * Объект у которого производится поиск обработчиков
     *
     * @var object
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private object $entity;

    /**
     * @param object $entity Объект у которого производится поиск обработчиков
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Создание объектов события
     *
     * @return EventInterface[]
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function make(): array
    {
        return array_map(function(array $item) {
            /** @var ReflectionMethod $method */
            $method = $item['method'];

            /** @var Attribute $attribute */
            $attribute = $item['attribute'];

            $context = $method->isStatic() ? null : $this->entity;
            $callback = $method->getClosure($context);

            return new Event($attribute->getType()->value, $callback);
        }, $this->parsing());
    }

    /**
     * Чтение доступных обработчиков
     *
     * @return ReflectionMethod[]
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function parsing(): array
    {
        $handlers = [];
        $chain = array_reverse(class_parents($this->entity));
        $chain[] = $this->entity;

        foreach ($chain as $entity) {
            $reflection = new ReflectionClass($entity);
            $methods = $reflection->getMethods();

            foreach ($methods as $method) {
                /** @var Attribute|null $attribute */
                if ($attribute = Entity::getAttributeByMethod($method, Attribute::class)) {
                    $handlers[] = [
                        'method' => $method,
                        'attribute' => $attribute,
                    ];
                }
            }
        }

        return $handlers;
    }
}