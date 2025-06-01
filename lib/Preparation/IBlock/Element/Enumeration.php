<?php

namespace Sholokhov\Exchange\Preparation\IBlock\Element;

use ReflectionException;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Factory\Result\SimpleFactory;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Fields\IBlock\ElementFieldInterface;
use Sholokhov\Exchange\Preparation\IBlock\PropertyTrait;
use Sholokhov\Exchange\Preparation\Base\AbstractEnumeration;
use Sholokhov\Exchange\Target\IBlock\Property\PropertyEnumeration;

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
            'iblock_id' => $this->iblockId,
            'property_code' => $field->getTo(),
        ]);
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     * @version 1.0.0
     * @since 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && ($property = $this->getPropertyRepository()->get($field->getTo()))
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_LIST
            && !$property['USER_TYPE'];
    }
}