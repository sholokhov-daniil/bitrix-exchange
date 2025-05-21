<?php

namespace Sholokhov\BitrixExchange\Validators;

use TypeError;

use Sholokhov\BitrixExchange\Messages\Type\Error;
use Sholokhov\BitrixExchange\Messages\Type\Result;
use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Messages\ResultInterface;

/**
 * Проверка стандартной карты обмена
 *
 * @package Validator
 * @since 1.0.0
 * @version 1.0.0
 */
class MapValidator implements ValidatorInterface
{
    /**
     * Валидация карты обмена
     *
     * @param mixed $value
     * @return ResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function validate(mixed $value): ResultInterface
    {
        if (!is_array($value)) {
            throw new TypeError("Value must be an array");
        }

        $primary = false;
        $result = new Result;

        foreach ($value as $field) {
            if (!($field instanceof FieldInterface)) {
                $result->addError(new Error('Incorrect field description', 400, $field));
                break;
            }

            if ($field->isPrimary()) {
                if ($primary) {
                    $result->addError(new Error('Duplication of the identification', 400, $field));
                } else {
                    $primary = true;
                }
            }

            if ($field->getOut() === '') {
                $result->addError(new Error('Field path is required', 400, $field));
            }
        }

        if (!$primary) {
            $result->addError(new Error('No identification field', 400));
        }

        return $result;
    }
}