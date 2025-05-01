<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractNumber;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Приведение значения свойства к целочисленному значению
 */
class Number extends AbstractNumber
{
    use PropertyTrait;

    /**
     * @param int $iBlockID ИБ в рамках которого производится преобразование
     */
    public function __construct(int $iBlockID)
    {
        $this->iblockId = $iBlockID;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return bool
     * @throws LoaderException
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && $field->isProperty()
            && ($property = $this->getRepository()->get($field->getCode()))
            && (!$property['USER_TYPE'] && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_NUMBER);
    }
}