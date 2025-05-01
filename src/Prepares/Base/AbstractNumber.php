<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

/**
 * Приводит значение к целочисленному
 */
abstract class AbstractNumber extends AbstractPrepare
{
    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return int
     */
    protected function logic(mixed $value, FieldInterface $field): int
    {
        return (int)$value;
    }
}