<?php

namespace Sholokhov\Exchange\Normalizers;

use DateTimeZone;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

/**
 * Нормализация данных, связанных с датой и время
 */
class DateNormalizer
{
    /**
     * Приведение произвольного значения даты в объект даты bitrix
     *
     * @param array|string|int $date
     * @param DateTimeZone|null $timeZone
     * @return Date|array
     * @throws \DateMalformedStringException
     */
    public static function createDate(array|string|int $date, DateTimeZone $timeZone = null): Date|array
    {
        if (is_array($date)) {
            return array_map(fn($value) => Date::createFromPhp(new \DateTime($value, $timeZone)), $date);
        }

        return Date::createFromPhp(new \DateTime($date));
    }

    /**
     * Приведение произвольного значения даты в объект даты и времени bitrix
     *
     * @param array|string|int $date
     * @param DateTimeZone|null $timeZone
     * @return DateTime|array
     * @throws \DateMalformedStringException
     */
    public static function createDateTime(array|string|int $date, DateTimeZone $timeZone = null): DateTime|array
    {
        if (is_array($date)) {
            return array_map(fn($value) => DateTime::createFromPhp(new \DateTime($value, $timeZone)), $date);
        }

        return DateTime::createFromPhp(new \DateTime($date, $timeZone));
    }
}