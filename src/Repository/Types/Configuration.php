<?php

namespace Sholokhov\Exchange\Repository\Types;

/**
 * Хранилище конфигураций
 */
class Configuration extends Memory
{
    /**
     * @param string $name Группа конфигурации
     */
    public function __construct(string $name)
    {
        $configuration = $this->load($name);
        parent::__construct($configuration);
    }

    /**
     * Объект хранения конфигураций обмена
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getOptionEntity(string $name, mixed $default = null): mixed
    {
        return $this->getValueByGroup('options', $name, $default);
    }

    /**
     * Объект хранения кэша обмена
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getCacheEntity(string $name, mixed $default = null): mixed
    {
        return $this->getValueByGroup('cache', $name, $default);
    }

    /**
     * Получение значение конфигурации
     *
     * @param string $group
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getValueByGroup(string $group, string $name, mixed $default = null): mixed
    {
        return $this->fields[$group][$name] ?? $this->fields[$group]['default'] ?? $default;
    }

    /**
     * Загрузка конфигурации
     *
     * @param string $name
     * @return array
     */
    private function load(string $name): array
    {
        $path = $this->getDirectory() . DIRECTORY_SEPARATOR . $name . '.ini';
        return (array)parse_ini_file($path);
    }

    /**
     * Директория хранения конфигурационных файлов
     *
     * @return string
     */
    private function getDirectory(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config';
    }
}