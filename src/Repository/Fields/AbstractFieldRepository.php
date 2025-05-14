<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use Exception;

use Sholokhov\BitrixExchange\Repository\Types\Memory;
use Sholokhov\BitrixExchange\Repository\RepositoryInterface;

use Psr\Container\ContainerInterface;

/**
 * @version 1.0.0
 */
abstract class AbstractFieldRepository implements ContainerInterface
{
    /**
     * Хранилище данных
     *
     * @var RepositoryInterface[]
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private static array $storage;

    /**
     * Конфигурация хранилища
     *
     * @var Memory
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private Memory $options;

    /**
     * Проверка валидности конфигураций.
     *
     * Если конфигурации не валидны, то необходимо вызвать исключение
     *
     * @extends Exception
     * @param array $options Валидируемая конфигурация хранилища
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function checkOptions(array $options): void;

    /**
     * Обновление информации определенного свойства
     *
     * @param string $code
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract public function refreshByCode(string $code): void;

    /**
     * Загрузка данных о свойствах
     *
     * @param array $parameters Параметры запроса
     * @return array
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function query(array $parameters = []): array;

    /**
     * Идентификатор хранилища (уникальный ID)
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function getHash(): string;

    /**
     * @param array $options Конфигурация хранилища
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(array $options)
    {
        $this->checkOptions($options);
        $this->options = new Memory($this->normalizeOptions($options));
    }

    /**
     * Получение информации о свойствах в виде массива
     *
     * @return array
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function toArray(): array
    {
        return $this->getStorage()->toArray();
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
        array_walk($fields, fn($field, $code) => $container->set($code, $fields));
    }

    /**
     * Получения информации поля
     *
     * @param string $id Код или ID свойства
     * @param mixed|null $default Значение по умолчанию
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function get(string $id, mixed $default = null): mixed
    {
        return $this->getStorage()->get($id, $default);
    }

    /**
     * @param string $id Код или ID свойства
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function has(string $id): bool
    {
        return $this->getStorage()->has($id);
    }

    /**
     * Нормализация(обработка) конфигураций хранилища
     *
     * Метод был создан для возможности переопределения
     *
     * @param array $options
     * @return array
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function normalizeOptions(array $options): array
    {
        return $options;
    }

    /**
     * Получение конфигурации
     *
     * @return RepositoryInterface
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function getOptions(): RepositoryInterface
    {
        return $this->options;
    }

    /**
     * Получения хранилища данных
     *
     * @final
     * @return Memory
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    final protected function getStorage(): Memory
    {
        return self::$storage[$this->getHash()] ??= new Memory($this->query());
    }
}