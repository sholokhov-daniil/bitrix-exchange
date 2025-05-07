<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use CFile;

use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Sholokhov\Exchange\Fields\FieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Производит преобразование пути до файла в формат, который поддерживается свойствами информационного блока
 *
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
     * @throws LoaderException
     * @version 1.0.0
     * @since 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && $field->isProperty()
            && ($property = $this->getRepository()->get($field->getCode()))
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
        $file = CFile::MakeFileArray($value);
        return ['VALUE' => $file, 'DESCRIPTION' => $file['name']];
    }
}