<?php

namespace Sholokhov\BitrixExchange\Prepares;

use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Prepares\PrepareInterface;

/**
 * Базовый класс преобразователей данных
 */
abstract class AbstractPrepare implements PrepareInterface
{
    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     */
    abstract protected function logic(mixed $value, FieldInterface $field): mixed;

    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     */
    public function prepare(mixed $value, FieldInterface $field): mixed
    {
        $value = FieldHelper::normalizeValue($value, $field);

        if (is_array($value)) {
            $result = array_map(fn($chit) => $this->logic($chit, $field), array_filter($value));
            $result = array_filter($result);
        } else {
            $result = $this->logic($value, $field);
        }

        return $result;
    }
}