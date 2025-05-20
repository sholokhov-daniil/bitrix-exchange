<?php

namespace Sholokhov\BitrixExchange\Preparation\IBlock\Element;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Preparation\Base\AbstractDate;
use Sholokhov\BitrixExchange\Preparation\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Приведение значения к типу @see \Bitrix\Main\Type\Date
 *
 * @package Preparation
 * @version 1.0.0
 * @since 1.0.0
 */
class Date extends AbstractDate
{
    use PropertyTrait;

    /**
     * @param int $iBlockID ИБ в рамках которого производится преобразование
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iBlockID)
    {
        $this->iblockId = $iBlockID;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        $code = $field->getCode();

        if ($field instanceof ElementFieldInterface) {
            $property = $this->getPropertyRepository()->get($code);
            return $property && $property['USER_TYPE'] === PropertyTable::USER_TYPE_DATE;
        }

        return $code === 'DATE_ACTIVE_FROM' || $code === 'DATE_ACTIVE_TO' || $code === 'TIMESTAMP_X';
    }
}