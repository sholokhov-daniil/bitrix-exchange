<?php

namespace Sholokhov\Exchange\Repository\Fields;

use Sholokhov\Exchange\Repository\AbstractRepository;

/**
 * @version 1.0.0
 *
 * @package Repository
 */
abstract class AbstractFieldRepository extends AbstractRepository implements FieldRepositoryInterface
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
    public function refreshById(string $id): void
    {
        $field = $this->search($id);
        $field ? $this->getStorage()->set($id, $field) : $this->getStorage()->delete($id);
    }

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
        array_walk($fields, fn($field, $id) => $container->set($id, $fields));
    }
}