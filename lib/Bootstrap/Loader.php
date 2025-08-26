<?php

namespace Sholokhov\Exchange\Bootstrap;

use ReflectionClass;
use ReflectionException;
use Sholokhov\Exchange\Target\Attributes\Configuration;

/**
 * Производит вызов всех методов отвечающий за загрузку конфигураций обмена
 *
 * @package Bootstrap
 * @since 1.0.0
 * @version 1.0.0
 */
class Loader
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
     * Выполнить загрузку
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function bootstrap(): void
    {
        $chain = array_reverse(class_parents($this->exchange));
        $chain[] = $this->exchange;
        array_walk($chain, [$this, 'run']);
    }

    /**
     * Автозагрузка конфигураций объекта
     *
     * @param string|object $entity
     * @return void
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function run(string|object $entity): void
    {
        $reflection = new ReflectionClass($entity);
        $methods = $reflection->getMethods();

        foreach ($methods as $method) {
            if ($method->getAttributes(Configuration::class)) {
                $method->invoke($this->exchange);
            }
        }
    }
}