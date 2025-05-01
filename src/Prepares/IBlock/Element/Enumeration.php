<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use Bitrix\Main\Diag\Debug;
use ReflectionException;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractEnumeration;
use Sholokhov\BitrixExchange\Target\IBlock\Property\PropertyEnumeration;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

class Enumeration extends AbstractEnumeration
{
    use PropertyTrait;

    /**
     * @param int $iBlockID ИБ в рамках которого производится преобразование
     */
    public function __construct(int $iBlockID, string $primary = 'VALUE')
    {
        $this->iblockId = $iBlockID;
        parent::__construct($primary);
    }

    /**
     * Инициализация импорта элементов списка
     *
     * @param FieldInterface $field
     * @return ExchangeInterface
     * @throws ReflectionException
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        return new PropertyEnumeration([
            'entity_id' => $this->iblockId,
            'property_code' => $field->getCode(),
        ]);
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
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_LIST
            && !$property['USER_TYPE'];
    }
}