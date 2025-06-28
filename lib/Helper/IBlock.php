<?php

namespace Sholokhov\Exchange\Helper;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CIBlockRights;
use Sholokhov\Exchange\Cache\OrmCache;

class IBlock
{
    /**
     * Получение доступных инфоблоков
     *
     * @param array{permission: string, order: array, filter: array, select: array, limit: int} $parameters
     * @return array
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws \DateInvalidOperationException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getAvailableIBlock(array $parameters = []): array
    {
        if (!Loader::includeModule('iblock')) {
            return [];
        }

        $permission = $parameters['permission'] ?? 'iblock_admin_display';

        $query = IblockTable::query()->addFilter('ACTIVE', 'Y');

        if ($order = $parameters['order'] ?? []) {
            $query->setOrder($order);
        }

        if ($limit = $parameters['limit'] ?? null) {
            $query->setLimit($limit);
        }

        if ($select = $parameters['select'] ?? []) {
            if (!in_array('ID', $select)) {
                $select[] = 'ID';
            }

            $query->setSelect($select);
        } else {
            $query->setSelect(['*']);
        }

        if ($filter = $parameters['filter'] ?? []) {
            foreach ($filter as $key => $value) {
                $key = is_numeric($key) ? null : $key;
                $query->addFilter($key, $value);
            }
        }

        $query->setCacheTtl(360000);
        $iterator = $query->exec();
        $cacheKey = serialize($order) . serialize($filter) . serialize($select);

        $cache = new OrmCache(IblockTable::getEntity());
        return $cache->has($cacheKey)
            ? $cache->get($cacheKey, [])
            : $cache->setInvoke($cacheKey, function () use ($iterator, $permission) {
                $result = [];

                while ($iBlock = $iterator->fetch()) {
                    if (CIBlockRights::UserHasRightTo($iBlock['ID'], $iBlock['ID'], $permission)) {
                        $result[$iBlock['ID']] = $iBlock;
                    }
                }

                return $result;
            });
    }

    /**
     * Получение доступных типов
     *
     * @param array{filter: array, order: array, select: array, language: string, permission: string} $parameters
     * @return array
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws \DateInvalidOperationException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getAvailableTypes(array $parameters = []): array
    {
        if (!Loader::includeModule('iblock')) {
            return [];
        }

        $language = $parameters['language'] ?? Context::getCurrent()->getLanguage();
        $permission = $parameters['permission'] ?? 'iblock_admin_display';

        $availableIBlocks = self::getAvailableIBlock([
            'permission' => $permission,
            'select' => ['IBLOCK_TYPE_ID']
        ]);

        $accessTypes = array_unique(array_column($availableIBlocks, 'IBLOCK_TYPE_ID'));

        if (empty($accessTypes)) {
            return [];
        }

        $query = TypeTable::query()
            ->addSelect('*')
            ->addFilter('LANG_MESSAGE.LANGUAGE_ID', $language)
            ->addFilter('ID', $accessTypes)
            ->setCacheTtl(36000);

        if ($select = $parameters['select'] ?? []) {
            $query->setSelect($select);
        }

        if ($order = $parameters['order'] ?? []) {
            $query->setOrder($order);
        }

        if ($filter = $parameters['filter'] ?? []) {
            foreach ($filter as $key => $value) {
                $key = is_numeric($key) ? null : $key;
                $query->addFilter($key, $value);
            }
        }

        return $query->exec()->fetchAll();
    }
}