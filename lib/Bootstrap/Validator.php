<?php

namespace Sholokhov\Exchange\Bootstrap;

use ReflectionClass;
use ReflectionException;

use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Target\Attributes\Validate;

/**
 * Производит вызов методов отвечающих за валидацию обмена
 *
 * @package Bootstrap
 */
class Validator
{
    /**
     * @param object $exchange
     * @return ResultInterface
     * @throws ReflectionException
     */
    public function validate(object $exchange): ResultInterface
    {
        $result = new Result;

        $chain = array_reverse(class_parents($exchange));
        $chain[] = $exchange;

        foreach ($chain as $entity) {
            $reflection = new ReflectionClass($entity);
            $methods = $reflection->getMethods();

            foreach ($methods as $method) {
                if ($method->getAttributes(Validate::class)) {
                    $validateResult = $method->invoke($exchange);

                    if ($validateResult instanceof ResultInterface) {
                        $result->addErrors($validateResult->getErrors());
                    }
                }
            }
        }

        return $result;
    }
}