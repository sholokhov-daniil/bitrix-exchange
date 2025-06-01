<?php

namespace Sholokhov\BitrixExchange\Helper;

use Illuminate\Support\Arr;

/**
 * @package Helper
 */
class Helper
{
    /**
     * Псевдо-идентификатор модуля
     *
     * @return string
     */
    public static function getModuleID(): string
    {
        return 'sholokhov.exchange';
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