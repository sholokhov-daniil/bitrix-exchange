<?php

namespace Sholokhov\Exchange\Normalizers;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\MappingExchangeInterface;

/**
 * Нормализует импортируемые значения
 */
class ValueNormalizer
{
    public function __construct(private readonly MappingExchangeInterface $exchange)
    {
    }

    /**
     * Нормализовать значение
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     */
    public function normalize(mixed $value, FieldInterface $field): mixed
    {
        $isMultiple = $this->exchange->isMultipleField($field);

        if ($isMultiple && !is_array($value)) {
            $value = $value === null ? [] : [$value];
        } elseif (!$isMultiple && is_array($value)) {
            $value = reset($value);
        }

        if (is_callable($field->getNormalizer())) {
            $value = ($field->getNormalizer())($value, $field);
        }

        return $value;
    }
}