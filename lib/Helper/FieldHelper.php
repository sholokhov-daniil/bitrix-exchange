<?php

namespace Sholokhov\Exchange\Helper;

use Illuminate\Support\Arr;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Fields\TreeField;

class FieldHelper
{
    /**
     * Получение значения свойства
     *
     * @param array $item
     * @param FieldInterface $field
     * @return mixed
     */
    public static function getValue(array $item, FieldInterface $field): mixed
    {
        if ($field instanceof TreeField) {
            return self::getTreeValue($item, $field);
        }

        return Helper::getArrValueByPath($item, $field->getPath());
    }

    /**
     * Получение значение свойства ветвления
     *
     * @param array $item
     * @param TreeField $field
     * @return array|null
     */
    public static function getTreeValue(array $item, TreeField $field): ?array
    {
        $result = [];

        $root = Helper::getArrValueByPath($item, $field->getPath());

        if (!is_array($root)) {
            return null;
        }

        $childrenField = $field->getChildren();
        foreach ($root as $children) {
            if ($childrenField instanceof TreeField) {
                $result = array_merge($result, self::getTreeValue($children, $childrenField));
            } else {
                $result[] = self::getValue($item, $childrenField);
            }
        }

        return $result;
    }
}