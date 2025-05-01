<?php

namespace Sholokhov\BitrixExchange\Target\IBlock;

use CIBlock;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Target\Attributes\Validate;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Импорт в информационный блок
 */
abstract class IBlock extends Exchange
{
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
     * @final
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
     * @final
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
        return (int)$this->getOptions()->get('entity_id');
    }

    /**
     * Проверка загрузки модулей
     *
     * @return ResultInterface
     * @throws LoaderException
     */
    #[Validate]
    private function checkModules(): ResultInterface
    {
        $result = new DataResult;

        if (!Loader::includeModule('iblock')) {
            $result->addError(new Error('Module "iblock" not installed'));
        }
        return $result;
    }

    /**
     * Валидация конфигурации обмена
     *
     * @return ResultInterface
     */
    #[Validate]
    private function validateOptions(): ResultInterface
    {
        $result = new DataResult;

        if ($this->getIBlockID() <= 0) {
            $result->addError(new Error('IBLOCK ID is required'));
        }

        return $result;
    }
}