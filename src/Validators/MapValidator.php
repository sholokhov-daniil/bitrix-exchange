<?php

namespace Sholokhov\Exchange\Validators;

use TypeError;

use Sholokhov\Exchange\Fields\Field;
use Bitrix\Main\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

/**
 * Проверка стандартной карты обмена
 */
class MapValidator implements Validator
{
    /**
     * Валидация карты обмена
     *
     * @param mixed $value
     * @return Result
     */
    public function validate(mixed $value): Result
    {
        if (!is_array($value)) {
            throw new TypeError("Value must be an array");
        }

        $primary = false;
        $result = new DataResult;

        foreach ($value as $field) {
            if (!($field instanceof Field)) {
                $result->addError(new Error('Incorrect field description'));
                break;
            }

            if ($field->isKeyField()) {
                if ($primary) {
                    $result->addError(new Error(sprintf('Duplication of the identification field "%s"', $field->getCode())));
                } else {
                    $primary = true;
                }
            }

            if (!$field->getPath()) {
                $result->addError(new Error('Field path is required'));
            }
        }

        if (!$primary) {
            $result->addError(new Error('No identification field'));
        }

        return $result;
    }
}