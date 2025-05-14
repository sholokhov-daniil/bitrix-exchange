<?php

namespace Sholokhov\BitrixExchange\Validators;

use Sholokhov\BitrixExchange\Messages\ResultInterface;

/**
 * Производит валидацию передаваемого значения
 *
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