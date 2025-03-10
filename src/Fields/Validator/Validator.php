<?php

namespace Sholokhov\Exchange\Fields\Validator;

use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Messages\Errors\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

/**
 * Проверка стандартной карты обмена
 */
class Validator
{
    /**
     * Валидация карты обмена
     *
     * @param array $map
     * @return Result
     */
    public function validate(array $map): Result
    {
        $primary = false;
        $result = new DataResult;

        foreach ($map as $field) {
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