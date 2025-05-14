<?php

namespace Sholokhov\BitrixExchange\Repository\IBlock;

use _CIBElement;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CIBlockElement;
use Psr\Container\ContainerInterface;
use Sholokhov\BitrixExchange\Repository\Types\Memory;

/**
 * @version 1.0.0
 */
class ElementRepository implements ContainerInterface
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

    public function get(string $id)
    {
        // TODO: Implement get() method.
    }

    public function has(string $id): bool
    {
        // TODO: Implement has() method.
    }

    /**
     * Поиск элемента
     *
     * @param string $xmlId
     * @return _CIBElement|null
     * @throws LoaderException
     * @since 1.0.0
     * @version 1.0.0
     */
    private function query(string $xmlId): ?_CIBElement
    {
        if (!Loader::includeModule('iblock')) {
            throw new LoaderException('Module "iblock" not installed');
        }

        return CIBlockElement::GetList([], ['XML_ID' => $xmlId])->GetNextElement() ?: null;
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