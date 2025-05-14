<?php

namespace Sholokhov\BitrixExchange\Validators;

use TypeError;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Messages\Type\Error;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;

/**
 * Проверка стандартной карты обмена
 *
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
        $result = new DataResult;

        foreach ($value as $field) {
            if (!($field instanceof FieldInterface)) {
                $result->addError(new Error('Incorrect field description'));
                break;
            }

            if ($field->isPrimary()) {
                if ($primary) {
                    $result->addError(new Error(sprintf('Duplication of the identification field "%s"', $field->getCode())));
                } else {
                    $primary = true;
                }
            }

            if ($field->getPath() === '') {
                $result->addError(new Error('Field path is required'));
            }
        }

        if (!$primary) {
            $result->addError(new Error('No identification field'));
        }

        return $result;
    }
}