<?php

namespace Sholokhov\Exchange\Fields\Validator;

use Sholokhov\Exchange\Messages\Result;

/**
 * Производит проверку карты соответствия, для возможности обмена
 */
interface MapValidator
{
    /**
     * Проверить карту
     *
     * @param array $map
     * @return Result
     */
    public function validate(array $map): Result;
}