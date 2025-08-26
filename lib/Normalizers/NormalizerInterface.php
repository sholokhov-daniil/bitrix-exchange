<?php

namespace Sholokhov\Exchange\Normalizers;

use Sholokhov\Exchange\Fields\FieldInterface;

interface NormalizerInterface
{
    /**
     * Нормализовать значение
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     */
    public function normalize(mixed $value, FieldInterface $field): mixed;
}