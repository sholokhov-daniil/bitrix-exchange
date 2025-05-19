<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use ReflectionException;

use Sholokhov\BitrixExchange\Factory\Result\SimpleFactory;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractIBlockSection;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Target\IBlock\Section;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Fields\FieldInterface;

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
class IBlockSection extends AbstractIBlockSection
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
     * Инициализация импорта элементов информационного блока
     *
     * @param FieldInterface $field Свойство в которое производится преобразование
     * @return ExchangeInterface
     *
     * @throws ReflectionException
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        $property = $this->getPropertyRepository()->get($field->getCode());
        return new Section([
            'result_repository' => new SimpleFactory,
            'entity_id' => $property['LINK_IBLOCK_ID']
        ]);
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
        $property = $this->getPropertyRepository()->get($field->getCode());
        return (int)($property['LINK_IBLOCK_ID'] ?? 0);
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
        return $field instanceof ElementFieldInterface
            && ($property = $this->getPropertyRepository()->get($field->getCode()))
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_SECTION
            && !$property['USER_TYPE']
            && $property['LINK_IBLOCK_ID'];
    }
}