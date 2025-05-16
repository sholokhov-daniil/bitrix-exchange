<?php

namespace Sholokhov\BitrixExchange\Bootstrap;

use ReflectionClass;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;
use Sholokhov\BitrixExchange\Target\Attributes\Validate;

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
     * @throws \ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function run(): ResultInterface
    {
        $result = new DataResult;

        $chain = array_reverse(class_parents($this->exchange));
        $chain[] = $this->exchange;

        foreach ($chain as $entity) {
            $reflection = new ReflectionClass($entity);
            $methods = $reflection->getMethods();

            foreach ($methods as $method) {
                if ($method->getAttributes(Validate::class)) {
                    $validateResult = $method->invoke($this->exchange);

                    if ($validateResult instanceof DataResult) {
                        $result->addErrors($validateResult->getErrors());
                    }
                }
            }
        }

        return $result;
    }
}