<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use ReflectionException;

use Sholokhov\BitrixExchange\Target\IBlock\Element;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractIBlockElement;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\FieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Преобразует значение имеющего связь к элементу информационного блока
 *
 * Если элемент будет отсутствовать, то будет произведено автоматическое создание
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class IBlockElement extends AbstractIBlockElement
{
    /**
     * Инициализация импорта элементов информационного блока
     *
     * @param FieldInterface $field Свойство в которое производится преобразование
     * @return ExchangeInterface
     *
     * @throws LoaderException
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        $property = $this->getRepository()->get($field->getCode());
        return new Element(['entity_id' => $property['LINK_IBLOCK_ID']]);
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
            && ($property = $this->getRepository()->get($field->getCode()))
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_ELEMENT
            && !$property['USER_TYPE']
            && $property['LINK_IBLOCK_ID'];
    }
}