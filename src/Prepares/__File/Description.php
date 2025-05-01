<?php

namespace Sholokhov\BitrixExchange\Prepares\File;

use CFile;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Prepares\PrepareInterface;

/**
 * Производит преобразование пути до файла в описывающий массив
 * Если значение множественное, то вернет массив описаний
 */
class Description implements PrepareInterface
{
    private array $supportedFields = [];

    /**
     * Преобразование пути до файла в описывающий массив
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return array
     */
    public function prepare(mixed $value, FieldInterface $field): array
    {
        $value = FieldHelper::normalizeValue($value, $field);

        if (is_array($value)) {
            $result = array_map([$this, 'prepareItem'], array_filter($value));
            $result = array_filter($result);
        } else {
            $result = $this->prepareItem($value);
        }

        return $result;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return bool
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return in_array($field->getCode(), $this->supportedFields);
    }

    /**
     * Добавление поддерживаемого свойства
     *
     * @param string $code
     * @return $this
     */
    public function addSupportedField(string $code): self
    {
        $this->supportedFields[] = $code;
        return $this;
    }

    /**
     * Преобразование пути в описывающий массив
     *
     * @param mixed $path
     * @return array
     */
    private function prepareItem(mixed $path): array
    {
        return CFile::MakeFileArray($path) ?: [];
    }
}