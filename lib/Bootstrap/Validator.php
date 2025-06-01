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
 * @since 1.0.0
 * @version 1.0.0
 */
class Validator
{
    /**
     * @param object $exchange
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(private readonly object $exchange)
    {
    }

    /**
     * @return ResultInterface
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function run(): ResultInterface
    {
        $result = new Result;

        $chain = array_reverse(class_parents($this->exchange));
        $chain[] = $this->exchange;

        foreach ($chain as $entity) {
            $reflection = new ReflectionClass($entity);
            $methods = $reflection->getMethods();

            foreach ($methods as $method) {
                if ($method->getAttributes(Validate::class)) {
                    $validateResult = $method->invoke($this->exchange);

                    if ($validateResult instanceof ResultInterface) {
                        $result->addErrors($validateResult->getErrors());
                    }
                }
            }
        }

        return $result;
    }
}