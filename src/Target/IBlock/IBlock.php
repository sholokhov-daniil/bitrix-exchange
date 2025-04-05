<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CIBlock;
use ReflectionException;

use Bitrix\Main\Error;
use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages\ResultInterface;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Импорт в информационный блок
 */
abstract class IBlock extends Exchange
{
    /**
     * Проверка возможности выполнения обмена
     *
     * @return ResultInterface
     * @throws LoaderException
     * @throws ReflectionException
     */
    protected function validate(): ResultInterface
    {
        $result = parent::validate();

        if (!Loader::includeModule('iblock')) {
            $result->addError(new Error('Module "iblock" not installed'));
        }

        if ($this->getIBlockID() <= 0) {
            $result->addError(new Error('IBLOCK ID is required'));
        }

        return $result;
    }

    /**
     * Обработка конфигураций обмена
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        $options['iblock_id'] = (int)$options['iblock_id'];
        return parent::normalizeOptions($options);
    }

    /**
     * Очистка кэша ИБ
     *
     * @return void
     */
    final protected function cleanCache(): void
    {
        CIBlock::CleanCache($this->getIBlockID());
        CIBlock::clearIblockTagCache($this->getIBlockID());
    }

    /**
     * Получение информации ИБ
     *
     * @return array
     */
    final protected function getIBlockInfo(): array
    {
        return CIBlock::GetArrayByID($this->getIBlockID()) ?: [];
    }

    /**
     * Информационный блок в который идет импорт
     *
     * @final
     * @return int
     */
    final protected function getIBlockID(): int
    {
        return (int)$this->getOptions()->get('iblock_id');
    }
}