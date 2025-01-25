<?php

namespace Sholokhov\Exchange\Helper;

use Illuminate\Support\Arr;

class Helper
{
    /**
     * Получение значения по пути из ключей массива
     *
     * @param array $item
     * @param string $path
     * @return mixed
     */
    public static function getArrValueByPath(array $item, $path): mixed
    {
        return Arr::get($item, $path);
    }
}