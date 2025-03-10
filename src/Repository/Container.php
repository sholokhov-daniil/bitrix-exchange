<?php

namespace Sholokhov\Exchange\Repository;

use Sholokhov\Exchange\Bootstrap\Configuration;

/**
 * Глобальные конфигурации обмена данных
 *
 */
final class Container implements Repository
{
    private static self $instance;

    /**
     * Значения контейнера
     *
     * @var array
     */
    private array $instances = [];

    /**
     * Псевдонимы значений
     *
     * @var array
     */
    private array $aliases = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
            (new Configuration)->bootstrap(self::$instance);
        }

        return self::$instance;
    }

    /**
     * Получение хранимого значения по идентификатору
     *
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        $id = $this->getAlias($id);
        return $this->instances[$id] ?? null;
    }

    /**
     * Запись с указанным идентификатором существует
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || $this->isAlias($id);
    }

    /**
     * Является строка псевдонимом
     *
     * @param string $id
     * @return bool
     */
    public function isAlias(string $id): bool
    {
        return isset($this->aliases[$id]);
    }

    /**
     * Получение псевдонима идентификатора
     *
     * @param string $id
     * @return string
     */
    public function getAlias(string $id): string
    {
        return isset($this->aliases[$id]) ? $this->getAlias($this->aliases[$id]) : $id;
    }

    /**
     * Установка значения
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setField(string $name, mixed $value): void
    {
        $name = $this->getAlias($name);
        $this->instances[$name] = $value;
    }

    /**
     * Получение значения
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getField(string $name, mixed $default = null): mixed
    {
        $name = $this->getAlias($name);
        return $this->instances[$name] ?? $default;
    }

    /**
     * Проверка наличия значения
     *
     * @param string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        $name = $this->getAlias($name);
        return array_key_exists($name, $this->instances);
    }

    /**
     * Получение конфигурации обмена
     *
     * @return Repository|null
     */
    public function getConfiguration(): ?Repository
    {
        return $this->getField('configuration');
    }

    public function current(): mixed
    {
        return current($this->instances);
    }

    public function next(): void
    {
        next($this->instances);
    }

    public function key(): mixed
    {
        return key($this->instances);
    }

    public function valid(): bool
    {
        return $this->key() !== false;
    }

    public function rewind(): void
    {
        reset($this->instances);
    }

    public function count(): int
    {
        return count($this->instances);
    }
}