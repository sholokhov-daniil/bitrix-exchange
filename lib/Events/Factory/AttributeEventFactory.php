<?php

namespace Sholokhov\Exchange\Events\Factory;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Target\Attributes\Event as Attribute;

/**
 * Создает объекты события, которые зарегистрированы в объекте посредством атрибутов
 *
 * @package Event
 */
readonly class AttributeEventFactory
{
    /**
     * Объект у которого производится поиск обработчиков
     *
     * @var object
     */
    private object $entity;

    /**
     * @param object $entity Объект у которого производится поиск обработчиков
     */
    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Создание объектов события
     *
     * @return callable[][]
     * @throws ReflectionException
     */
    public function make(): array
    {
        $result = [];

        foreach ($this->parsing() as $item) {
            /** @var ReflectionMethod $method */
            $method = $item['method'];

            /** @var Attribute $attribute */
            $attribute = $item['attribute'];

            $context = $method->isStatic() ? null : $this->entity;
            $callback = $method->getClosure($context);
            $type = $attribute->getType()->value;


            $result[$type][] = $callback;
        }

        return $result;
    }

    /**
     * Чтение доступных обработчиков
     *
     * @return ReflectionMethod[]
     * @throws ReflectionException
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