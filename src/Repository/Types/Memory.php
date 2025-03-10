<?php

namespace Sholokhov\Exchange\Repository\Types;

use Sholokhov\Exchange\Repository\RepositoryInterface;

/**
 * Базовое представление контейнера.
 *
 * @internal
 * @implements RepositoryInterface
 */
class Memory implements RepositoryInterface
{
    /**
     * Хранимые значения.
     *
     * @var array
     */
    protected array $fields = [];

    public function __construct(array $fields = [])
    {
        array_walk($fields, fn($value, $key) => $this->setField($key, $value));
    }

    public function toArray(): array
    {
        return $this->fields;
    }

    /**
     * Количество записей в контейнере.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * Указание значения.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setField(string $name, mixed $value): void
    {
        $this->fields[$name] = $value;
    }

    /**
     * Получение значения свойства.
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getField(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->fields) ? $this->fields[$name] : $default;
    }

    /**
     * Проверка наличия свойства.
     *
     * @param string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * Получение текущего значения.
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->fields);
    }

    /**
     * Передвинуть указатель вперед.
     *
     * @return void
     */
    public function next(): void
    {
        next($this->fields);
    }

    /**
     * Получение текущего ключа.
     *
     * @return string|int|null
     */
    public function key(): string|int|null
    {
        return key($this->fields);
    }

    /**
     * Проверка корректного положения каретки.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return array_key_exists($this->key(), $this->fields);
    }

    /**
     * передвинуть каретку в начало списка.
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->fields);
    }

    /**
     * Получение значения свойства.
     *
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->getField($id);
    }

    /**
     * Проверка наличия свойства.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->hasField($id);
    }
}