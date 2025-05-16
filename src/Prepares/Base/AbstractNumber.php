<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

/**
 * Приводит значение к целочисленному
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
    protected function logic(mixed $value, FieldInterface $field): int
    {
        return (int)$value;
    }
}