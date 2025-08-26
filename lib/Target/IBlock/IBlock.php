<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CIBlock;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Repository\IBlock\IBlockRepository;
use Sholokhov\Exchange\Target\Attributes\Validate;
use Sholokhov\Exchange\Target\Attributes\Configuration;

use Sholokhov\Exchange\Messages\Type\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Импорт в информационный блок
 * @package Target
 */
abstract class IBlock extends Exchange
{
    /**
     * Информационный блок в который идет импорт
     *
     * @final
     * @return int
     */
    final public function getIBlockID(): int
    {
        return (int)$this->getOptions()->get('iblock_id');
    }

    /**
     * Получение информации об информационном блоке
     *
     * @return IBlockRepository|null
     *
     * @final
     * @since 1.0.0
     * @version 1.0.0
     */
    final public function getIBlockInfo(): ?IBlockRepository
    {
        return $this->repository->get('iblock_info');
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
     * @final
     * @return void
     */
    final protected function cleanCache(): void
    {
        CIBlock::CleanCache($this->getIBlockID());
        CIBlock::clearIblockTagCache($this->getIBlockID());
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
        $result = new Result;

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
        $result = new Result;

        if ($this->getIBlockID() <= 0) {
            $result->addError(new Error('IBLOCK ID is required'));
        }

        return $result;
    }

    /**
     * Инициализация хранилища информации об информационном блоке
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[Configuration]
    private function bootstrapIBlockRepository(): void
    {
        $this->repository->set('iblock_info', new IBlockRepository($this->getIBlockID()));
    }
}