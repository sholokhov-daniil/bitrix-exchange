<?php

namespace Sholokhov\BitrixExchange\Events;

use InvalidArgumentException;

/**
 * Подписка на событие
 * @deprecated Будет использоваться bitrix
 */
class Subscribe
{
    /**
     * @param string $event Событие, которому относится обработчик
     * @param mixed $callback Обработчик события
     * @param int $sort Сортировка обработчика
     */
    public function __construct(
        protected string $event,
        protected mixed $callback,
        protected int $sort = 500
    )
    {
        if (!is_callable($this->callback)) {
            throw new InvalidArgumentException("Callback method not supported");
        }
    }

    /**
     * Наименование события которому относится подписка
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * Обработчик события
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Получение сортировка обработчика
     *
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }
}