<?php

namespace Sholokhov\BitrixExchange\Repository\Types;

use Sholokhov\BitrixExchange\Repository\RepositoryInterface;

/**
 * Базовое представление контейнера.
 *
 * @internal
 * @implements RepositoryInterface
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Memory implements RepositoryInterface
{
    /**
     * Хранимые значения.
     *
     * @var array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected array $fields = [];

    /**
     * @param array $fields
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(array $fields = [])
    {
        array_walk($fields, fn($value, $key) => $this->set($key, $value));
    }

    /**
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function toArray(): array
    {
        return $this->fields;
    }

    /**
     * Количество записей в контейнере.
     *
     * @return int
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * Указание значения.
     *
     * @param string $id
     * @param mixed $value
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function set(string $id, mixed $value): void
    {
        $this->fields[$id] = $value;
    }

    /**
     * Получение значения свойства.
     *
     * @param string $id
     * @param mixed|null $default
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function get(string $id, mixed $default = null): mixed
    {
        return array_key_exists($id, $this->fields) ? $this->fields[$id] : $default;
    }

    /**
     * Получение текущего значения.
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function current(): mixed
    {
        return current($this->fields);
    }

    /**
     * Передвинуть указатель вперед.
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function next(): void
    {
        next($this->fields);
    }

    /**
     * Получение текущего ключа.
     *
     * @return string|int|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function key(): string|int|null
    {
        return key($this->fields);
    }

    /**
     * Проверка корректного положения каретки.
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function valid(): bool
    {
        return array_key_exists($this->key(), $this->fields);
    }

    /**
     * передвинуть каретку в начало списка.
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function rewind(): void
    {
        reset($this->fields);
    }

    /**
     * Проверка наличия свойства.
     *
     * @param string $id
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->fields);
    }

    /**
     * Удаление значения
     *
     * @param string $id
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function delete(string $id): void
    {
        unset($this->fields[$id]);
    }

    /**
     * Очистить хранилище
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function clear(): void
    {
        $this->fields = [];
    }
}