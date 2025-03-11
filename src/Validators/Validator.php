<?php

namespace Sholokhov\Exchange\Validators;

use Sholokhov\Exchange\Messages\Result;

/**
 * Производит валидацию передаваемого значения
 */
interface Validator
{
    /**
     * Валидация значения
     *
     * @param mixed $value
     * @return Result
     */
    public function validate(mixed $value): Result;
}