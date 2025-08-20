<?php

namespace Sholokhov\Exchange\Events;

use ReflectionException;
use Sholokhov\Exchange\Events\Factory\AttributeEventFactory;

/**
 * Менеджер внутренних изолированных событий
 *
 * @package Event
 * @since 1.0.0
 * @version 1.0.0
 */
class EventManager
{
    /**
     * Зарегистрированные события
     *
     * @var EventInterface[][]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private array $events = [];

    /**
     * Инициализация диспетчера на основе объекта
     *
     * @param object $entity
     * @return static
     * @throws ReflectionException
     */
    public static function create(object $entity): static
    {
        $dispatcher = new static;
        $events = (new AttributeEventFactory($entity))->make();
        $dispatcher->registrationBulk($events);

        return $dispatcher;
    }

    /**
     * Вызов событий
     *
     * @param string $type
     * @param ...$args
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function send(string $type, ...$args): array
    {
        if (!isset($this->events[$type])) {
            return [];
        }

        return array_map(fn(EventInterface $event) => $event->call(...$args), $this->events[$type]);
    }

    /**
     * Регистрация события
     *
     * @param EventInterface $event
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function registration(EventInterface $event): self
    {
        $this->events[$event->getType()][] = $event;
        return $this;
    }

    /**
     * Регистрация списка событий
     *
     * @param array $events
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function registrationBulk(array $events): self
    {
        array_walk($events, $this->registration(...));
        return $this;
    }
}