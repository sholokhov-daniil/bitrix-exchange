<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use DateTime;
use DateMalformedStringException;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Bitrix\Main\Type\Date as BxDate;

/**
 * Приведение значения к объекту {@see BxDate}
 */
abstract class AbstractDate extends AbstractPrepare
{
    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return BxDate
     * @throws DateMalformedStringException
     */
    protected function logic(mixed $value, FieldInterface $field): BxDate
    {
        return BxDate::createFromPhp(new DateTime($value));
    }
}