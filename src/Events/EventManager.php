<?php

namespace Sholokhov\BitrixExchange\Events;

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
     * @var array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private array $events = [];

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

        return array_map(fn(Event $event) => $event->send(...$args), $this->events[$type]);
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
}