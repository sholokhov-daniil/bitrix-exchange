<?php

namespace Sholokhov\Exchange\Normalizers;

use DateTime;
use DateTimeZone;

use Bitrix\Main\Type\Date as BxDate;
use Bitrix\Main\Type\DateTime as BxDateTime;

/**
 * Нормализация данных, связанных с датой и время
 */
class DateNormalizer
{
    /**
     * Приведение произвольного значения даты в стандартный объект даты и время
     *
     * @param array|string|int $date
     * @param DateTimeZone|null $timeZone
     * @return DateTime|array
     * @throws \DateMalformedStringException
     */
    public static function createDate(array|string|int $date, DateTimeZone $timeZone = null): DateTime|array
    {
        return is_array($date) ? array_map(fn($value) => new DateTime($value, $timeZone), $date) : new DateTime($date, $timeZone);
    }

    /**
     * Приведение произвольного значения даты в объект даты bitrix
     *
     * @param array|string|int $date
     * @param DateTimeZone|null $timeZone
     * @return BxDate|array
     * @throws \DateMalformedStringException
     */
    public static function createBxDate(array|string|int $date, DateTimeZone $timeZone = null): BxDate|array
    {
        if (is_array($date)) {
            return array_map(fn($value) => BxDate::createFromPhp(new DateTime($value)), $date);
        }

        return BxDate::createFromPhp(new DateTime($date));
    }

    /**
     * Приведение произвольного значения даты в объект даты и времени bitrix
     *
     * @param array|string|int $date
     * @param DateTimeZone|null $timeZone
     * @return BxDateTime|array
     * @throws \DateMalformedStringException
     */
    public static function createBxDateTime(array|string|int $date, DateTimeZone $timeZone = null): BXDateTime|array
    {
        if (is_array($date)) {
            return array_map(fn($value) => BXDateTime::createFromPhp(new DateTime($value, $timeZone)), $date);
        }

        return BXDateTime::createFromPhp(new DateTime($date, $timeZone));
    }
}