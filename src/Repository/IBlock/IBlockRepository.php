<?php

namespace Sholokhov\BitrixExchange\Repository\IBlock;

use CIBlock;

use Sholokhov\BitrixExchange\Repository\Types\Memory;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

use Psr\Container\ContainerInterface;

/**
 * Хранилище информации информационного блока
 *
 * Хранилище является статическим - при разрушении объекта загруженные данные останутся жить в памяти.
 * Если необходимо освободить память, то необходимо вызвать метод {@see self::free}.
 * @final
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @package Repository
 */
final class IBlockRepository implements ContainerInterface
{
    /**
     * Хранилище данных
     *
     * @var Memory
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private static Memory $storage;

    /**
     * @param int $iBlockID Информационный блок с которого необходимо получать данные
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(private readonly int $iBlockID)
    {
    }

    /**
     * Получение основной информации ИБ
     *
     * @param string $id
     * @param mixed|null $default
     * @return mixed
     * @throws LoaderException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function get(string $id,  mixed $default = null): mixed
    {
        return $this->getDescription()[$id] ?? $default;
    }

    /**
     * Проверка наличия пункта основного описания ИБ
     *
     * @param string $id
     * @return bool
     * @throws LoaderException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function has(string $id): bool
    {
        return isset($this->getDescription()[$id]);
    }

    /**
     * Получение информации о свойствах информационного блока
     *
     * @return PropertyRepository|null
     * @throws LoaderException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getProperties(): ?PropertyRepository
    {
        $description = $this->getDescription();

        if (!$description) {
            return null;
        }

        $description['PROPERTIES'] = new PropertyRepository($description['IBLOCK_ID']);
        $this->getStorage()->set($this->iBlockID, $description);

        return $description['PROPERTIES'];
    }

    /**
     * Освобождение памяти.
     *
     * При повторной попытке получить данные свойства произойдет загрузка данных
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function free(): void
    {
        if ($this->getStorage()->has($this->iBlockID)) {
            $this->getStorage()->delete($this->iBlockID);
        }
    }

    /**
     * Получение основного описания информационного блока
     *
     * @return array
     * @throws LoaderException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getDescription(): array
    {
        $storage = $this->getStorage();

        if (!$storage->has($this->iBlockID)) {
            $storage->set($this->iBlockID, $this->getInfo());
        }

        return $storage->get($this->iBlockID, []);
    }

    /**
     * Получение основной информации
     *
     * @return array
     * @throws LoaderException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function getInfo(): array
    {
        if (!Loader::includeModule('iblock')) {
            throw new LoaderException('iblock module is not installed');
        }

        if (!$this->iBlockID) {
            return [];
        }

        return CIBlock::GetArrayByID($this->iBlockID) ?: [];
    }

    /**
     * Получение хранилища данных свойств инфоблока
     *
     * @return Memory
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function getStorage(): Memory
    {
        return self::$storage ??= new Memory;
    }
}