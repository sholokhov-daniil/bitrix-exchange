<?php

namespace Sholokhov\BitrixExchange\Repository;

use Exception;
use Sholokhov\BitrixExchange\Repository\Types\Memory;

/**
 * @since 1.0.0
 * @version 1.0.0
 *
 * @package Repository
 */
abstract class AbstractRepository implements RepositoryInterface
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
     * Уникальный идентификатор хранилища
     *
     * @var string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private readonly string $id;

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
     * Генерация уникального идентификатора хранилища
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function generateId(): string;

    /**
     * Запрос на получение данных
     *
     * @param array $parameters Параметры запроса
     * @return array
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function query(array $parameters = []): array;

    /**
     * Подгрузить данные по идентификатору
     *
     * @param string $id
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    abstract protected function search(string $id): mixed;

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
     * @param array $options Конфигурация хранилища
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(array $options)
    {
        $this->checkOptions($options);
        $this->options = new Memory($this->normalizeOptions($options));
        $this->id = $this->generateId();
    }

    /**
     * Идентификатор хранилища (уникальный ID)
     *
     * @final
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    final public function getId(): string
    {
        return $this->id;
    }

    /**
     * Получение информации в формате ответа
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
     * Получения информации по идентификатору
     *
     * @param string $id Ключ хранения значения
     * @param mixed|null $default Значение по умолчанию
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function get(string $id, mixed $default = null): mixed
    {
        if ($this->has($id)) {
            return $this->getStorage()->get($id);
        }

        $value = $this->search($id) ?: $default;
        $this->set($id, $value);

        return $value;
    }

    /**
     * Установка значения
     *
     * @param string $id
     * @param mixed $value
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function set(string $id, mixed $value): void
    {
        $this->getStorage()->set($id, $value);
    }

    /**
     * @param string $id Ключ хранения
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
     * Удаления значения из хранилища по ключу
     *
     * @param string $id
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function delete(string $id): void
    {
        $this->getStorage()->delete($id);
    }

    /**
     * Очистка хранилища
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function clear(): void
    {
        $this->getStorage()->clear();
    }

    /**
     * Полная очистка хранилища
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function clearAll(): void
    {
        self::$storage = [];
    }

    /**
     * Значение на которое показывает указатель
     *
     * @return mixed
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function current(): mixed
    {
        return $this->getStorage()->current();
    }

    /**
     * Переместить указатель вперед на один шаг
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function next(): void
    {
        $this->getStorage()->next();
    }

    /**
     * Ключ на который ссылается указатель
     *
     * @return int|null|string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function key(): int|null|string
    {
        return $this->getStorage()->key();
    }

    /**
     * Проверка, что указатель ссылается на существующий ключ
     *
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function valid(): bool
    {
        return $this->getStorage()->valid();
    }

    /**
     * Переместить указатель в конец
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function rewind(): void
    {
        $this->getStorage()->rewind();
    }

    /**
     * Количество значений, которое хранится в хранилище
     *
     * @return int
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function count(): int
    {
        return $this->getStorage()->count();
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
        return self::$storage[$this->getId()] ??= new Memory;
    }
}