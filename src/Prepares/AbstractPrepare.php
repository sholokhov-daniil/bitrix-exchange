<?php

namespace Sholokhov\BitrixExchange\Prepares;

use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Prepares\PrepareInterface;

/**
 * Базовый класс преобразователей данных
 *
 * @version 1.0.0
 * @since 1.0.0
 */
abstract class AbstractPrepare implements PrepareInterface
{
    /**
     * Преобразование значения
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function logic(mixed $value, FieldInterface $field): mixed;

    /**
     * Преобразование значения
     *
     * @param mixed $value Значение, которое необходимо преобразовать. Может принимать массив
     * @param FieldInterface $field Свойство, которое необходимо преобразовать
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
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

        return $this->after($result, $field);
    }

    /**
     * Нормализация конечного значения
     *
     * Вызывается после нормализации значения(-ий).
     * Метод создан для переопределения потомками
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function after(mixed $value, FieldInterface $field): mixed
    {
        return $value;
    }
}