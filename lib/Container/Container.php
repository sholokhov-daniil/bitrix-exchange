<?php

namespace Sholokhov\Exchange\Container;

/**
 * Базовое представление контейнера.
 * Контейнер производит зранение различных значений.
 * С контейнером можно работать как с массивом (частично).
 *
 * Класс реализует паттерн "контейнер"
 * @link https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%BD%D1%82%D0%B5%D0%B9%D0%BD%D0%B5%D1%80_%D1%81%D0%B2%D0%BE%D0%B9%D1%81%D1%82%D0%B2_(%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD_%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
 *
 * @internal
 * @implements ContainerInterface
 * @autdor Daniil S.
 */
class Container implements ContainerInterface
{
    /**
     * Хранимые значения.
     *
     * @var array
     *
     * @autdor Daniil S. GlobalArts
     */
    protected array $fields = [];

    public function __construct(array $fields = [])
    {
        array_walk($fields, [$this, 'setField']);
    }

    public function __serialize(): array
    {
        return $this->fields;
    }

    public function __unserialize(array $data): void
    {
        $this->fields = $data;
    }

    public function toArray(): array
    {
        return $this->fields;
    }

    /**
     * Количество записей в контейнере.
     *
     * @return int
     *
     * @autdor Daniil S. GlobalArts
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * Задаёт данные, которые должны быть сериализованы в JSON.
     *
     * @return array
     *
     * @autdor Daniil S. GlobalArts
     */
    public function jsonSerialize(): array
    {
        return $this->fields;
    }

    /**
     * Представление контейнера в виде сериализованной строки.
     *
     * @return string
     *
     * @autdor Daniil S. GlobalArts
     */
    public function serialize(): string
    {
        return serialize($this->fields);
    }

    /**
     * Десериализация данных.
     *
     * @param string $data
     * @return ContainerInterface
     *
     * @autdor Daniil S. GlobalArts
     */
    public function unserialize(string $data): ContainerInterface
    {
        $this->fields = unserialize($data);
        return $this;
    }

    /**
     * Указание значения.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @author Daniil S. GlobalArts
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
     * @author Daniil S. GlobalArts
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
     * @author Daniil S. GlobalArts
     */
    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * Получение текущего значения.
     *
     * @return mixed
     *
     * @author Daniil S. GlobalArts
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
     * @author Daniil S. GlobalArts
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
     * @author Daniil S. GlobalArts
     */
    public function key(): string|int|null
    {
        return key($this->fields);
    }

    /**
     * Проверка корректного положения каретки..
     *
     * @return bool
     *
     * @author Daniil S. GlobalArts
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
     * @author Daniil S. GlobalArts
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
     *
     * @author Daniil S. GlobalArts
     */
    public function get($id): mixed
    {
        return $this->getField($id);
    }

    /**
     * Проверка наличия свойства.
     *
     * @param string $id
     * @return bool
     *
     * @author Daniil S. GlobalArts
     */
    public function has($id): bool
    {
        return $this->hasField($id);
    }
}