<?php

namespace Sholokhov\BitrixExchange\Prepares;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
interface PrepareInterface
{
    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function prepare(mixed $value, FieldInterface $field): mixed;

    /**
     * Преобразование поддерживает свойство и значение
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool;
}