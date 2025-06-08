<?php

namespace Sholokhov\Exchange\Helper;

use Illuminate\Support\Arr;

/**
 * @package Helper
 * @version 1.2.0
 */
class Helper
{
    /**
     * Псевдо-идентификатор модуля
     *
     * @return string
     * @version 1.2.0
     */
    public static function getModuleID(): string
    {
        return GetModuleID(self::getRootDir());
    }

    /**
     * Путь до корня модуля
     *
     * @return string
     *
     * @version 1.2.0
     * @since 1.2.0
     */
    public static function getRootDir(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * Получение значения по пути из ключей массива
     *
     * @param array $item
     * @param string $path
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getArrValueByPath(array $item, $path): mixed
    {
        return Arr::get($item, $path);
    }
}