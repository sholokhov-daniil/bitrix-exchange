<?php

namespace Sholokhov\BitrixExchange\Repository\IBlock;

use CIBlock;

use Sholokhov\BitrixExchange\Repository\Types\Memory;

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\LoaderException;

use Psr\Container\ContainerInterface;

/**
 * Хранилище информации свойств информационного блока.
 * Хранилище является статическим - при разрушении объекта загруженные данные останутся жить в памяти.
 * Если необходимо освободить память, то необходимо вызвать метод {@see self::free}.
 *
 * Хранилище производит автоматическое обновление при изменении, удалении или добавлении свойства
 * @final
 *
 * @version 1.0.0
 *
 * @package Repository
 */
final class PropertyRepository implements ContainerInterface
{
    /**
     * Хранилище данных
     *
     * @var Memory
     */
    private static Memory $storage;

    /**
     * @param int $iBlockID
     */
    public function __construct(private readonly int $iBlockID)
    {
        if (!isset(self::$storage)) {
            $this->initEvents();
        }
    }

    /**
     * Получение информации о свойстве
     *
     * @param string $id
     * @param mixed|null $default
     * @return array|mixed
     * @throws LoaderException
     */
    public function get(string $id, mixed $default = null): mixed
    {
        return $this->getProperties()[$id] ?? $default;
    }

    /**
     * Проверка наличия свойства
     *
     * @param string $id
     * @return bool
     * @throws LoaderException
     */
    public function has(string $id): bool
    {
        return isset($this->getProperties()[$id]);
    }

    /**
     * Освобождение памяти.
     *
     * При повторной попытке получить данные свойства произойдет загрузка данных
     *
     * @return void
     */
    public function free(): void
    {
        if ($this->getStorage()->has($this->iBlockID)) {
            $this->getStorage()->delete($this->iBlockID);
        }
    }

    /**
     * Освобождение памяти у всех информационных блоков
     *
     * @return void
     */
    public function freeAll(): void
    {
        $this->getStorage()->clear();
    }

    /**
     * Обновление данных о свойствах
     *
     * @return void
     * @throws LoaderException
     */
    public function refresh(): void
    {
        $this->free();
        $this->getProperties();
    }

    /**
     * Получение информации по текущему информационному блоку
     *
     * @return array
     * @throws LoaderException
     */
    public function getProperties(): array
    {
        $storage = $this->getStorage();

        if (!$storage->has($this->iBlockID)) {
            $storage->set($this->iBlockID, $this->getInfo());
        }

        return $storage->get($this->iBlockID, []);
    }

    /**
     * Получение ID информационного блока которому принадлежит хранилище
     *
     * @return int
     */
    public function getIBlockID(): int
    {
        return $this->iBlockID;
    }

    /**
     * Загружает данные свойств инфоблока
     *
     * @return array
     * @throws LoaderException
     */
    private function getInfo(): array
    {
        $result = [];

        if (!Loader::includeModule('iblock')) {
            throw new LoaderException('iblock module is not installed');
        }

        if (!$this->iBlockID) {
            return [];
        }

        $iterator = CIBlock::GetProperties($this->iBlockID, [], ['ACTIVE' => 'Y']);
        while ($property = $iterator->Fetch()) {
            $result[$property['CODE']] = $property;
        }

        return $result;
    }

    /**
     * Получение хранилища данных свойств инфоблока
     *
     * @return Memory
     */
    private function getStorage(): Memory
    {
        return self::$storage ??= new Memory;
    }

    /**
     * Инициализация событий обновления контейнера
     *
     * @return void
     */
    private function initEvents(): void
    {
        $eventTypes = [
            'OnAfterIBlockPropertyAdd',
            'OnAfterIBlockPropertyUpdate',
            'OnAfterIBlockPropertyDelete',
        ];
        $manager = EventManager::getInstance();
        $storage = $this->getStorage();

        foreach ($eventTypes as $type) {
            $manager->addEventHandler(
                'iblock',
                $type,
                function(array $fields) use($storage) {
                    if ($storage->has($fields['IBLOCK_ID'])) {
                        $this->refresh();
                    }
                }
            );
        }
    }
}