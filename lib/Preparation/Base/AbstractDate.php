<?php

namespace Sholokhov\BitrixExchange\Preparation\Base;

use DateTime;
use DateMalformedStringException;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Preparation\AbstractPrepare;

use Bitrix\Main\Type\Date as BxDate;

/**
 * Приведение значения к объекту @see BxDate
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractDate extends AbstractPrepare
{
    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return BxDate|string
     * @throws DateMalformedStringException
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): BxDate|string
    {
        return $value ? BxDate::createFromPhp(new DateTime($value)) : '';
    }
}