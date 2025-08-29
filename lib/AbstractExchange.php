<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Dispatcher\ExternalEventDispatcher;
use Sholokhov\Exchange\Messages\DataResultInterface;

abstract class AbstractExchange extends AbstractApplication
{
    protected readonly ExternalEventDispatcher $externalEventDispatcher;

    /**
     * Логика добавления элемента сущности
     *
     * @param array $fields
     * @return DataResultInterface
     */
    abstract protected function doAdd(array $fields): DataResultInterface;

    /**
     * Логика обновления значения
     *
     * @param int $id
     * @param array $fields Обновляемый набор параметров
     * @return DataResultInterface
     */
    abstract protected function doUpdate(int $id, array $fields): DataResultInterface;

    /**
     * Логика проверки наличия элемента сущности
     *
     * @param array $item
     * @return bool
     */
    abstract protected function doExist(array $item): bool;

    /**
     * Преобразование данных, для добавления элемента сущности
     *
     * @param array $item
     * @return array
     */
    abstract protected function prepareForAdd(array $item): array;

    /**
     * Преобразование данных для обновления
     *
     * @param array $item
     * @return array
     */
    abstract protected function prepareForUpdate(array $item): array;

    /**
     * Получение ID элемента сущности
     *
     * @param array $item
     * @return int
     */
    abstract protected function resolveId(array $item): int;

    /**
     * Проверяет наличие элемента
     *
     * @final
     * @param array $item
     * @return bool
     */
    final public function exists(array $item): bool
    {
        return $this->resolveId($item) ?: $this->doExist($item);
    }

    /**
     * Обновление элемента сущности
     *
     * @final
     * @param array $item
     * @return DataResultInterface
     */
    final public function update(array $item): DataResultInterface
    {
       $fields = $this->prepareForUpdate($item);
       $id = $this->resolveId($item);

        $beforeUpdate = $this->externalEventDispatcher?->beforeUpdate($id, $fields);
        if (!$beforeUpdate->isSuccess()) {
            return $beforeUpdate;
        }

        if ($beforeUpdate->isStopped()) {
            return $beforeUpdate;
        }

        $result = $this->doUpdate($id, $item);
        $this->externalEventDispatcher?->afterUpdate($id, $fields, $result);

        return $result;
    }

    /**
     * Добавление элемента в сущность
     *
     * @final
     * @param array $item
     * @return DataResultInterface
     */
    final public function add(array $item): Messages\DataResultInterface
    {
        $fields = $this->prepareForAdd($item);

        $beforeAdd = $this->externalEventDispatcher?->beforeAdd($fields);
        if (!$beforeAdd->isSuccess()) {
            return $beforeAdd;
        }

        if ($beforeAdd->isStopped()) {
            return $beforeAdd;
        }

        $result = $this->doAdd($fields);
        $this->externalEventDispatcher->afterAdd($fields, $result);

        return $result;
    }
}