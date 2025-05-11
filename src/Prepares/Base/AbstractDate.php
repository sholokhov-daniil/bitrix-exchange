<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use DateTime;
use DateMalformedStringException;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Bitrix\Main\Type\Date as BxDate;

/**
 * Приведение значения к объекту @see BxDate
 *
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