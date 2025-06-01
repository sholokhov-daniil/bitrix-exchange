<?php

namespace Sholokhov\BitrixExchange\Preparation\Base;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Preparation\AbstractPrepare;

/**
 * Приводит значение к целочисленному
 *
 * Если значение не является числом, то преобразуется в null
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractNumber extends AbstractPrepare
{
    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return int
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): mixed
    {
        return is_numeric($value) ? $value : null;
    }
}