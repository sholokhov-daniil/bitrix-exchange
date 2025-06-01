<?php

namespace Sholokhov\BitrixExchange\Events;

/**
 * Описание структуры события
 *
 * @package Event
 * @since 1.0.0
 * @version 1.0.0
 */
interface EventInterface
{
    /**
     * Вызвать обработчик события
     *
     * @param ...$args
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function call(...$args): mixed;

    /**
     * Возвращает тип события
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getType(): string;
}