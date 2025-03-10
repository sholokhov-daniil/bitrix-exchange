<?php

namespace Sholokhov\Exchange\Helper;

use Sholokhov\Exchange\Fields\Field;

class FieldHelper
{
    /**
     * Получение значения свойства
     *
     * @param array $item
     * @param Field $field
     * @return mixed
     */
    public static function getValue(array $item, Field $field): mixed
    {
        return $field->getChildren() ? self::getTreeValue($item, $field) : Helper::getArrValueByPath($item, $field->getPath());
    }

    /**
     * Получение значение свойства ветвления
     *
     * @param array $item
     * @param Field $field
     * @return array|null
     */
    public static function getTreeValue(array $item, Field $field): ?array
    {
        $result = [];

        $root = Helper::getArrValueByPath($item, $field->getPath());

        if (!is_array($root)) {
            return null;
        }

        $childrenField = $field->getChildren();

        if (!array_is_list($root)) {
            $root = [$root];
        }

        foreach ($root as $children) {
            if ($childrenField->getChildren()) {
                $result = array_merge($result, self::getTreeValue($children, $childrenField));
            } elseif (is_array($children)) {
                $result[] = self::getValue($children, $childrenField);
            }
        }

        return $result;
    }
}