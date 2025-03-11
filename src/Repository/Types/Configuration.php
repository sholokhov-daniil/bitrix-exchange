<?php

namespace Sholokhov\Exchange\Repository\Types;

use Bitrix\Main\Diag\Debug;
use Illuminate\Support\Arr;
use Sholokhov\Exchange\Repository\Repository;

/**
 * Хранилище конфигураций
 */
class Configuration implements Repository
{
   protected static self $instance;
   private array $items = [];

   private function __construct()
   {
   }

   public static function getInstance(): self
   {
       return self::$instance ??= new self;
   }

    /**
     * Получение значения конфигурации
     *
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->getField($id);
    }

    /**
     * Получения значения конфигурации
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getField(string $name, mixed $default = null): mixed
    {
        if (is_array($name)) {
            return $this->getMany($name);
        }

        return Arr::get($this->items, $name, $default);
    }

    /**
     * Установка значения конфигурации.
     *
     * @param $key
     * @param $value
     * @return void
     * @author Daniil S. GlobalArts
     */

    public function setField(string $name, mixed $value): void
    {
        $keys = is_array($name) ? $name : [$name => $value];

        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }

    }

    /**
     * Получение множества значений конфигурации.
     *
     * @param $keys
     * @return array
     * @author Daniil S. GlobalArts
     */
    public function getMany($keys): array
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->items, $key, $default);
        }

        return $config;
    }


    public function has(string $id): bool
    {
        // TODO: Implement has() method.
    }

    public function current(): mixed
    {
        // TODO: Implement current() method.
    }

    public function next(): void
    {
        // TODO: Implement next() method.
    }

    public function key(): mixed
    {
        // TODO: Implement key() method.
    }

    public function valid(): bool
    {
        // TODO: Implement valid() method.
    }

    public function rewind(): void
    {
        // TODO: Implement rewind() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }

    public function hasField(string $name): bool
    {
        // TODO: Implement hasField() method.
    }
}