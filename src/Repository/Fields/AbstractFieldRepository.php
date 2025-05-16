<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use Sholokhov\BitrixExchange\Repository\AbstractRepository;

/**
 * @version 1.0.0
 *
 * @package Repository
 */
abstract class AbstractFieldRepository extends AbstractRepository
{
    /**
     * Обновление информации определенного свойства
     *
     * @param string $code
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract public function refreshByCode(string $code): void;

    /**
     * Обновление информации о свойствах
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function refresh(): void
    {
        $container = $this->getStorage();
        $container->clear();
        $fields = $this->query();
        array_walk($fields, fn($field, $code) => $container->set($code, $fields));
    }
}