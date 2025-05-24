<?php

namespace Sholokhov\BitrixExchange\Preparation\UserField;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Preparation\AbstractPrepare;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Преобразование значения в формат Да\Нет
 *
 * @package Preparation
 * @version 1.0.0
 * @since 1.0.0
 */
class Boolean extends AbstractPrepare implements LoggerAwareInterface
{
    use UFTrait, LoggerAwareTrait;

    /**
     * @param string $entityId ID сущности в рамках которого производится преобразование
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        $property = $this->getFieldRepository()->get($field->getTo());
        return $property && $property['USER_TYPE_ID'] === 'boolean';
    }

    /**
     * Логика преобразования значения
     *
     * @param mixed $value Преобразуемое значение
     * @param FieldInterface $field Свойство, которому принадлежит преобразуемое значение
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): bool
    {
        return (bool)$value;
    }
}