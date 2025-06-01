<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use Sholokhov\BitrixExchange\Repository\RepositoryInterface;

/**
 * @package Repository
 *
 * @version 1.0.0
 * @since 1.0.0
 */
interface FieldRepositoryInterface extends RepositoryInterface
{
    /**
     * Обновление информации определенного свойства по идентификатору
     *
     * @param string $id
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function refreshById(string $id): void;

    /**
     * Обновление информации о свойствах
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function refresh(): void;
}