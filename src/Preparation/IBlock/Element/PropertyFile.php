<?php

namespace Sholokhov\BitrixExchange\Preparation\IBlock\Element;

use CFile;

use Sholokhov\BitrixExchange\Preparation\AbstractPrepare;
use Sholokhov\BitrixExchange\Preparation\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Производит преобразование пути до файла в формат, который поддерживается свойствами информационного блока
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
class PropertyFile extends AbstractPrepare implements LoggerAwareInterface
{
    use PropertyTrait, LoggerAwareTrait;

    /**
     * @param int $iBlockID ИБ в рамках которого производится преобразование
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iBlockID)
    {
        $this->iblockId = $iBlockID;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && ($property = $this->getPropertyRepository()->get($field->getIn()))
            && $property['PROPERTY_TYPE'] === PropertyTable::TYPE_FILE
            && !$property['USER_TYPE'];
    }

    /**
     * Логика преобразования значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): array
    {
        if (empty($value)) {
            return [];
        }

        $file = CFile::MakeFileArray($value);
        return ['VALUE' => $file, 'DESCRIPTION' => $file['name']];
    }
}