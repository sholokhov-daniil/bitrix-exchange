<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use DateTime as PhpDateTime;
use DateMalformedStringException;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Bitrix\Main\Type\DateTime as BxDateTime;

/**
 * Приведение значения к объекту @see BxDateTime
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractDateTime extends AbstractPrepare
{
    /**
     * Преобразование значения
     *
     * @param mixed $value Преобразуемое значение
     * @param FieldInterface $field Свойство, которому принадлежит преобразуемое значение
     * @return BxDateTime|string
     * @throws DateMalformedStringException
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): BxDateTime|string
    {
        return $value ? BxDateTime::createFromPhp(new PhpDateTime($value)) : '';
    }
}