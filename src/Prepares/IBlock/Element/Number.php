<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractNumber;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Приведение значения свойства к целочисленному значению
 *
 * @version 1.0.0
 * @since 1.0.0
 */
class Number extends AbstractNumber
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
        return $field instanceof ElementFieldInterface
            && $field->isProperty()
            && ($property = $this->getPropertyRepository()->get($field->getCode()))
            && (!$property['USER_TYPE'] && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_NUMBER);
    }
}