<?php

namespace Sholokhov\Exchange\Factory;

use Exception;
use ReflectionException;

use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Target\Attributes\MapValidator;
use Sholokhov\Exchange\Validators\ValidatorInterface;

/**
 * Производит инициализацию валидатора карты обмена
 */
class MapValidatorFactory
{
    /**
     * Создать элемент
     *
     * @param ExchangeInterface $exchange
     * @return ValidatorInterface
     * @throws ReflectionException
     */
    public static function create(ExchangeInterface $exchange): ValidatorInterface
    {
        /** @var MapValidator $attribute */
        $attribute = Entity::getAttributeChain($exchange, MapValidator::class);
        $validator = $attribute->getEntity();

        if (!is_subclass_of($validator, ValidatorInterface::class)) {
            throw new Exception('Validator class must be subclass of ' . ValidatorInterface::class);
        }

        return new $validator;
    }
}