<?php

namespace Sholokhov\Exchange\Messages\Type;

/**
 * Результат вызова события обмена
 *
 * @version 1.0.0
 * @since 1.0.0
 */
class EventResult extends Result
{
    /**
     * Обмен значением остановлено
     *
     * @var bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private bool $stopped = false;

    /**
     * Проверка остановки обмена значением
     *
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function isStopped(): bool
    {
        return $this->stopped;
    }

    /**
     * Флаг остановки обмена значения
     *
     * @param bool $stopped
     * @return $this
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function setStopped(bool $stopped = true): self
    {
        $this->stopped = $stopped;
        return $this;
    }
}