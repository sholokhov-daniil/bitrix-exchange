<?php

namespace Sholokhov\BitrixExchange\Prepares\UserField;

use ReflectionException;

use Sholokhov\BitrixExchange\Target\IBlock\LinkElement;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractHandbookImport;

use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * Преобразует значение имеющего связь к элементу информационного блока
 *
 * Если элемент будет отсутствовать, то будет произведено автоматическое создание
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class IBlockElement extends AbstractHandbookImport
{
    /**
     * Инициализация импорта элементов информационного блока
     *
     * @param FieldInterface $field Свойство в которое производится преобразование
     * @return ExchangeInterface
     *
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        $property = $this->getFieldRepository()->get($field->getCode());
        return new LinkElement(['entity_id' => $property['SETTINGS']['IBLOCK_ID']]);
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
        return is_array($value) ? (int)reset($value) : 0;
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
        $property = $this->getFieldRepository()->get($field->getCode());
        return $property && $property['USER_TYPE_ID'] === 'iblock_element' && $property['SETTINGS']['IBLOCK_ID'];
    }
}