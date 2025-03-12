<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CIBlock;
use ReflectionException;

use Bitrix\Main\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\AbstractExchange;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Импорт в информационный блок
 */
abstract class IBlock extends AbstractExchange
{
    /**
     * Проверка возможности выполнения обмена
     *
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws LoaderException
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    protected function check(): Result
    {
        $result = new DataResult;

        if (!Loader::includeModule('iblock')) {
            $result->addError(new Error('Module "iblock" not installed'));
        }

        if ($this->getOptions()->get('iblock_id') <= 0) {
            $result->addError(new Error('IBLOCK ID is required'));
        }

        $parentResult = parent::check();
        if (!$parentResult->isSuccess()) {
            $result->addErrors($parentResult->getErrors());
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final protected function getIBlockInfo(): array
    {
        return CIBlock::GetArrayByID($this->getIBlockID());
    }

    /**
     * Информационный блок в который идет импорт
     *
     * @final
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final protected function getIBlockID(): int
    {
        return (int)$this->getOptions()->get('iblock_id');
    }
}