<?php

namespace Sholokhov\Exchange\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionException;
use Sholokhov\Exchange\Events\Factory\AttributeEventFactory;

/**
 * Менеджер внутренних изолированных событий
 *
 * @package Event
 */
class EventManager implements EventDispatcherInterface
{
//    /**
//     * Зарегистрированные события
//     *
//     * @var callable[][]
//     */
//    private array $listeners = [];
//
//    /**
//     * Инициализация диспетчера на основе объекта
//     *
//     * @param object $entity
//     * @return static
//     * @throws ReflectionException
//     */
//    public static function create(object $entity): static
//    {
//        $dispatcher = new static;
//        $listeners = (new AttributeEventFactory($entity))->make();
//        $dispatcher->listenBulk($listeners);
//
//        return $dispatcher;
//    }
//
//    /**
//     * Подписаться на событие
//     *
//     * @param string $type
//     * @param callable $listener
//     * @return $this
//     */
//    public function addListener(string $type, callable $listener): static
//    {
//        $this->listeners[$type][] = $listener;
//        return $this;
//    }
//
//    /**
//     * Регистрация списка событий
//     *
//     * @param array $events
//     * @return $this
//     */
//    public function listenBulk(array $events): self
//    {
//        array_walk(
//            $events,
//            fn (callable $listener, string $type) => $listener($type, $events)
//        );
//        return $this;
//    }
//
//    /**
//     * Вызов событий
//     *
//     * @param object $event
//     * @return void
//     */
//    public function dispatch(object $event): void
//    {
//        $result = [];
//
//        if (!isset($this->listeners[$type])) {
//            return $result;
//        }
//
//        return array_map(
//            fn (callable $listener) => $listener(...$args),
//            $this->listeners[$type]
//        );
//    }
}