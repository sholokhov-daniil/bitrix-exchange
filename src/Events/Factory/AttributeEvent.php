<?php

namespace Sholokhov\BitrixExchange\Events\Factory;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Sholokhov\BitrixExchange\Events\Event;
use Sholokhov\BitrixExchange\Events\EventInterface;

/**
 * Создает объекты события, которые зарегистрированы в объекте посредством атрибутов
 *
 * @package Event
 * @since 1.0.0
 * @version 1.0.0
 */
class AttributeEvent
{
    /**
     * @param object $entity Объект у которого производится поиск обработчиков
     * @param string $attribute Атрибут, которым обозначаются методы обработчика
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(
        private readonly object $entity,
        private readonly string $attribute,
        private readonly string $type
    )
    {
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
        return array_map(function(ReflectionMethod $method) {
            $context = $method->isStatic() ? null : $this->entity;
            $callback = $method->getClosure($context);

            return new Event($this->type, $callback);
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
                if ($method->getAttributes($this->attribute)) {
                    $handlers[] = $method;
                }
            }
        }

        return $handlers;
    }
}