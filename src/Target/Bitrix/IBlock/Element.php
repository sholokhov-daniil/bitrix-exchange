<?php

namespace Sholokhov\Exchange\Target\Bitrix\IBlock\Element;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CIBlockElement;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Messages\Errors\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

class Element extends AbstractExchange
{
    /**
     * Проверка возможности выполнения обмена
     *
     * @return Result
     * @throws LoaderException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

    protected function add(array $item): Result
    {
        $result = new DataResult;
        $iblock = new CIBlockElement;
        $itemId = $iblock->Add($item);

        if ($itemId) {
            // Добавляем все свойства
        } else {
            $result->addError(new Error('Error while adding IBLOCK element: ' . $iblock->getLastError()));
        }

        return $result;
    }

    protected function update(array $item): Result
    {
        // TODO: Implement update() method.
    }

    protected function exists(array $item): bool
    {
        // TODO: Implement exists() method.
    }
}