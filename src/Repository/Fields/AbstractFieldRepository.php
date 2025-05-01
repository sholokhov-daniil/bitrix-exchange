<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use Exception;

use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Repository\RepositoryInterface;

use Psr\Container\ContainerInterface;

abstract class AbstractFieldRepository implements ContainerInterface
{
    private Memory $container;
    private Memory $options;

    /**
     * Обновление информации определенного свойства
     *
     * @param string $code
     * @return void
     */
    abstract public function refreshByCode(string $code): void;

    /**
     * Загрузка данных о свойствах
     *
     * @param array $parameters Параметры запроса
     * @return array
     */
    abstract protected function query(array $parameters = []): array;

    /**
     * Проверка валидности конфигураций.
     *
     * Если конфигурации не валидны, то необходимо вызвать исключение
     *
     * @extends Exception
     * @param array $options
     * @return void
     */
    abstract protected function checkOptions(array $options): void;

    /**
     * @param array $options Конфигурация хранилища
     */
    public function __construct(array $options)
    {
        $this->checkOptions($options);
        $this->options = new Memory($this->normalizeOptions($options));
    }

    /**
     * Обновление информации о свойствах
     *
     * @return void
     */
    public function refresh(): void
    {
        $container = $this->getContainer();
        $container->clear();
        $fields = $this->query();
        array_walk($fields, fn($field, $code) => $container->set($code, $fields));
    }

    /**
     * Преобразование в массив свойств
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->getContainer() as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Получение информации о свойстве
     *
     * @param string $id
     * @param mixed|null $default
     * @return array|null
     */
     public function get(string $id, mixed $default = null): ?array
     {
         return $this->getContainer()->get($id, $default);
     }

    /**
     * Проверка наличия информации о свойстве
     *
     * @param string $id Символьный код свойства
     * @return bool
     */
     public function has(string $id): bool
     {
         return $this->getContainer()->has($id);
     }

    /**
     * Нормализация(обработка) конфигураций хранилища
     *
     * Метод был создан для возможности переопределения
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        return $options;
    }

    /**
     * Получение конфигурации
     *
     * @return RepositoryInterface
     */
    protected function getOptions(): RepositoryInterface
    {
        return $this->options;
    }

    /**
     * Получение хранилища данных
     *
     * @return RepositoryInterface
     */
    protected function getContainer(): RepositoryInterface
    {
        return $this->container ??= new Memory($this->query());
    }
}