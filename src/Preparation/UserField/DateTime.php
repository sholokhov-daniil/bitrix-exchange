<?php

namespace Sholokhov\BitrixExchange\Preparation\UserField;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Preparation\Base\AbstractDateTime;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Преобразование значения в дату @see \Bitrix\Main\Type\DateTime
 *
 * @package Preparation
 * @version 1.0.0
 * @since 1.0.0
 */
class DateTime extends AbstractDateTime implements LoggerAwareInterface
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
        $property = $this->getFieldRepository()->get($field->getCode());
        return $property && $property['USER_TYPE_ID'] === 'datetime';
    }
}