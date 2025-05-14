<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use CFile;

use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Преобразование пути до файла в массив формата $_FILES {@see CFile::MakeFileArray()}
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class File extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
        return in_array($field->getCode(), ['PREVIEW_PICTURE', 'DETAIL_PICTURE']);
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
        return !empty($value) ? CFile::MakeFileArray($value) : [];
    }
}