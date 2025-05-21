<?php

namespace Sholokhov\BitrixExchange\Preparation\UserField;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Preparation\Base\AbstractIBlockElement;

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
    use UFTrait;

    /**
     * @param string $entityID Сущность для которой производится преобразование
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $entityID, string $primary = 'XML_ID')
    {
        $this->entityId = $entityID;
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
        $property = $this->getFieldRepository()->get($field->getIn());
        return (int)($property['SETTINGS']['IBLOCK_ID'] ?? 0);
    }

    /**
     * Проверка возможности преобразовать значение свойства
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
        $property = $this->getFieldRepository()->get($field->getIn());
        return $property && $property['USER_TYPE_ID'] === 'iblock_element' && $property['SETTINGS']['IBLOCK_ID'];
    }
}