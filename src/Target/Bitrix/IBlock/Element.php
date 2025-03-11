<?php

namespace Sholokhov\Exchange\Target\Bitrix\IBlock;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CIBlockElement;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Fields\IBlock\ElementField;
use Sholokhov\Exchange\Messages\Errors\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

class Element extends AbstractExchange
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

    protected function add(array $item): Result
    {
        $result = new DataResult;
        $iblock = new CIBlockElement;

        $preparedItem = $this->prepareItem($item);
        $data = $preparedItem['FIELDS'];
        $data['IBLOCK_ID'] = $this->getIBlockID();
        $data['PROPERTY_VALUES'] = $preparedItem['PROPERTIES'] ?? [];

        $itemId = $iblock->Add($data);

        if ($itemId) {
            // TODO: записываем хэш
            $result->setData((int)$itemId);
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
        return false;
    }

    /**
     * Разделение импортируемых данных на группы
     *
     * @param array{FIELDS: array, PROPERTIES: array} $item
     * @return array|array[]
     */
    protected function prepareItem(array $item): array
    {
        $result = [
            'FIELDS' => [],
            'PROPERTIES' => []
        ];

        foreach ($this->getMap() as $field) {
            $group = 'FIELDS';

            if ($field instanceof ElementField && $field->isProperty()) {
                $group = 'PROPERTIES';
            }

            $result[$group][$field->getCode()] = $item[$field->getCode()];
        }

        Debug::dump($result);

        if (!isset($result['FIELDS']['NAME'])) {
            foreach ($this->getMap() as $field) {
                if ($field->isKeyField()) {
                    $result['FIELDS']['NAME'] = $item[$field->getCode()];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Информационный блок в который иден обмен
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getIBlockID(): int
    {
        return (int)$this->getOptions()->get('iblock_id');
    }
}