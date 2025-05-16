<?php

declare (strict_types=1);

namespace Sholokhov\BitrixExchange\Fields;

/**
 * Описание поля имеющий ссылку на сущность.
 *
 * Позволяет более детально настроить импорт значения свойства
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Field
 */
interface LinkFieldInterface extends FieldInterface
{
    /**
     * Попытаться импортировать, если значение не найдено
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isAppend(): bool;
}

