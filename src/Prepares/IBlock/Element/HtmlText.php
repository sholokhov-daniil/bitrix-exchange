<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use Bitrix\Main\Diag\Debug;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;
use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Преобразует значение в формат, который поддерживает HTML\Text
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
class HtmlText extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait, PropertyTrait;

    /**
     * @param int $iBlockId Идентификатор информационного блока свойства в которое будет происходить запись
     */
    public function __construct(int $iBlockId)
    {
        $this->iblockId = $iBlockId;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     *
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
            && $property['USER_TYPE'] === PropertyTable::USER_TYPE_HTML;
    }

    /**
     * Логика преобразования значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return array|string
     *
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): array|string
    {
        $property = $this->getPropertyRepository()->get($field->getCode());

        return $property['MULTIPLE'] === 'Y'
            ? ['VALUE' => ['TYPE' => 'HTML', 'TEXT' => $value]]
            : (string)$value;
    }

    /**
     * Обработка конечного преобразованного значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function after(mixed $value, FieldInterface $field): mixed
    {
        return $value || $value == 0 ? $value : null;
    }
}