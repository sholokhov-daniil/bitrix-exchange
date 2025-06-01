<?php

namespace Sholokhov\Exchange\Target\Attributes;

use Attribute;
use Sholokhov\Exchange\Events\ExchangeEvent;

/**
 * Производит указание, что метод подписан на системное событие обмена
 *
 * @package Attribute
 * @since 1.0.0
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Event {
    /**
     * @param ExchangeEvent $type Тип события
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(private readonly ExchangeEvent $type)
    {
    }

    /**
     * Получение типа события
     *
     * @return ExchangeEvent
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getType(): ExchangeEvent
    {
        return $this->type;
    }
}
