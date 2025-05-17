<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use ReflectionException;

use Sholokhov\BitrixExchange\Target\Highloadblock\Element;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractIBlockImport;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Fields\FieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Преобразует значение имеющего связь к элементу справочника
 *
 * Если элемент будет отсутствовать, то будет произведено автоматическое создание
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
class HandbookElement extends AbstractIBlockImport
{
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
        return new Element(['entity_id' => $property['LINK_IBLOCK_ID']]);
    }

    /**
     * Нормализация результата импорта значения
     *
     * @param mixed $value
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function normalize(mixed $value): int
    {
        return is_array($value) ? $this->normalize(reset($value)) : max((int)$value, 0);
    }

    /**
     * Проверка возможности преобразовать значение свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     * @throws LoaderException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && $field->isProperty()
            && ($property = $this->getPropertyRepository()->get($field->getCode()))
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_ELEMENT
            && $property['USER_TYPE'] === PropertyTable::USER_TYPE_DIRECTORY
            && $property['LINK_IBLOCK_ID'];
    }
}