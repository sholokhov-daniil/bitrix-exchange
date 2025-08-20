<?php

namespace Sholokhov\Exchange\Services;

use Exception;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Target\Attributes\MapValidator;
use Sholokhov\Exchange\Validators\ValidatorInterface;

class ExchangeMapValidator implements ValidatorInterface
{
    private ValidatorInterface $engine;

    public function __construct(ExchangeInterface $exchange)
    {
        $this->engine = $this->createEngine($exchange);
    }

    /**
     * Произвести валидацию значения
     *
     * @param mixed $value
     * @return ResultInterface
     */
    public function validate(mixed $value): ResultInterface
    {
        return $this->engine->validate($value);
    }

    /**
     * Создание
     *
     * @param ExchangeInterface $exchange
     * @return ValidatorInterface
     * @throws \ReflectionException
     */
    private function createEngine(ExchangeInterface $exchange): ValidatorInterface
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