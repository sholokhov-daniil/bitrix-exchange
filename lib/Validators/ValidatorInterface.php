<?php

namespace Sholokhov\Exchange\Validators;

use Sholokhov\Exchange\Messages\ResultInterface;

/**
 * Производит валидацию передаваемого значения
 *
 * @package Validator
 * @since 1.0.0
 * @version 1.0.0
 */
interface ValidatorInterface
{
    /**
     * Валидация значения
     *
     * @param mixed $value
     * @return ResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function validate(mixed $value): ResultInterface;
}