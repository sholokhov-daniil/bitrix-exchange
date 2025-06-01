<?php

namespace Sholokhov\BitrixExchange\Events;

/**
 * Описание внутреннего события обмена
 *
 * @package Event
 * @since 1.0.0
 * @version 1.0.0
 */
class Event implements EventInterface
{
    /**
     * Тип события
     *
     * @var string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private string $type;

    /**
     * Обработчик события
     *
     * @var callable
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private $handler;

    /**
     * @param string $type Тип события
     * @param callable $handler Обработчик события
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $type, callable $handler)
    {
        $this->type = $type;
        $this->handler = $handler;
    }

    /**
     * Вызвать обработчик события
     *
     * @param ...$args
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function call(...$args): mixed
    {
        return call_user_func_array($this->handler, $args);
    }

    /**
     * Возвращает тип события
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getType(): string
    {
        return $this->type;
    }
}