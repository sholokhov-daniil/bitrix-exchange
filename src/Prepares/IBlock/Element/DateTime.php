<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractDateTime;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Приведение значения к типу {@see \Bitrix\Main\Type\DateTime}
 */
class DateTime extends AbstractDateTime
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
        $code = $field->getCode();

        if ($field instanceof ElementFieldInterface && $field->isProperty()) {
            $property = $this->getRepository()->get($code);
            return $property && $property['USER_TYPE'] === PropertyTable::USER_TYPE_DATETIME;
        }

        return $code === 'DATE_ACTIVE_FROM' || $code === 'DATE_ACTIVE_TO' || $code === 'TIMESTAMP_X';
    }
}