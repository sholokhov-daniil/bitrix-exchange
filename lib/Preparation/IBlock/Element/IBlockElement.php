<?php

namespace Sholokhov\BitrixExchange\Preparation\IBlock\Element;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;
use Sholokhov\BitrixExchange\Preparation\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Preparation\Base\AbstractIBlockElement;

use Bitrix\Iblock\PropertyTable;

/**
 * Преобразует значение имеющего связь к элементу информационного блока
 *
 * Если элемент будет отсутствовать, то будет произведено автоматическое создание
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
class IBlockElement extends AbstractIBlockElement
{
    use PropertyTrait;

    /**
     * @param int $iblockId Информационный блок, которому относится свойство хранения значения
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iblockId, string $primary = 'XML_ID')
    {
        $this->iblockId = $iblockId;
        parent::__construct($primary);
    }

    /**
     * Предоставляет идентификатор информационного блока в котором должен храниться элемент информационного блока
     *
     * @param FieldInterface $field Свойство из которого необходимо получить идентификатор информационного блока
     * @return int
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getFieldIBlockID(FieldInterface $field): int
    {
        $property = $this->getPropertyRepository()->get($field->getTo());
        return (int)($property['LINK_IBLOCK_ID'] ?? 0);
    }

    /**
     * Проверка возможности преобразовать значение свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     * @since 1.0.0
     * @version 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && ($property = $this->getPropertyRepository()->get($field->getTo()))
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_ELEMENT
            && !$property['USER_TYPE']
            && $property['LINK_IBLOCK_ID'];
    }
}