<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use ReflectionException;

use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Factory\Result\SimpleFactory;
use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractEnumeration;
use Sholokhov\BitrixExchange\Target\IBlock\Property\PropertyEnumeration;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Производит импорт значения списка и преобразовывает значение в идентификатор значения списка
 *
 * @package Preparation
 * @version 1.0.0
 * @since 1.0.0
 */
class Enumeration extends AbstractEnumeration
{
    use PropertyTrait;

    /**
     * @param int $iBlockID ИБ в рамках которого производится преобразование
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iBlockID, string $primary = 'VALUE')
    {
        $this->iblockId = $iBlockID;
        parent::__construct($primary);
    }

    /**
     * Инициализация импорта элементов списка
     *
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return ExchangeInterface
     * @throws ReflectionException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        return new PropertyEnumeration([
            'result_repository' => new SimpleFactory,
            'entity_id' => $this->iblockId,
            'property_code' => $field->getCode(),
        ]);
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
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_LIST
            && !$property['USER_TYPE'];
    }
}