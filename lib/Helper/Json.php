<?php

namespace Sholokhov\Exchange\Helper;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
class Json
{
    /**
     * Приведение данных в поддерживаемый формат при записи в таблицу
     *
     * @param mixed $value
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function encode(mixed $value): string
    {
        if (is_string($value)) {
            return json_validate($value) ? $value : '';
        }

        return json_encode($value);
    }

    /**
     * Приведение данных в поддерживаемый формат при получении данных
     *
     * @param mixed $value
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function decode(mixed $value): array
    {
        if (!is_string($value)) {
            return [];
        }

        return json_validate($value) ? json_decode($value, true) : [];
    }
}