<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CIBlock;
use CIBlockElement;
use ReflectionException;

use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Fields\IBlock\ElementField;
use Sholokhov\Exchange\Messages\Errors\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Импортирование элемента информационного блока
 */
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

    /**
     * Проверка наличия элемента
     *
     * @param array $item
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getKeyField();

        if (!$keyField || !isset($item[$keyField->getCode()])) {
            return false;
        }

        if ($this->cache->has($item[$keyField->getCode()])) {
            return true;
        }

        // TODO: Добавлять хэш импорта
        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            'ACTIVE' => 'Y',
        ];

        if ($keyField instanceof ElementField) {
            $filter['PROPERTY_' . $keyField->getCode()] = $item[$keyField->getCode()];
        } else {
            $filter[$keyField->getCode()] = $item[$keyField->getCode()];
        }

        if ($element = CIBlockElement::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->setField($item[$keyField->getCode()], $element);

            return true;
        }

        return false;
    }

    /**
     * Добавление элемента в информационный блок
     *
     * @param array $item
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function add(array $item): Result
    {
        $result = new DataResult;
        $iblock = new CIBlockElement;

        $preparedItem = $this->prepareItem($item);
        $data = $preparedItem['FIELDS'];
        $data['IBLOCK_ID'] = $this->getIBlockID();
        $data['PROPERTY_VALUES'] = $preparedItem['PROPERTIES'] ?? [];

        // TODO: Событие перед добавлением

        $itemId = $iblock->Add($data);

        if ($itemId) {
            // TODO: записываем хэш
            $result->setData((int)$itemId);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $itemId));

            if ($keyField = $this->getKeyField()) {
                $this->cache->setField($item[$keyField->getCode()], $data);
            }
        } else {
            $result->addError(new Error('Error while adding IBLOCK element: ' . $iblock->getLastError(), 500, $data));
        }

        // TODO: Событие после добавления

        return $result;
    }

    /**
     * Обновление элемента
     *
     * @param array $item
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function update(array $item): Result
    {
        $result = new DataResult;

        // TODO: Событие перед обновлением

        $keyField = $this->getKeyField();

        if (!$keyField) {
            return $result->addError(new Error('Error while updating IBLOCK element: No identification field'));
        }

        $iBlock = new CIBlockElement;
        $itemID = (int)($this->cache->getField($item[$keyField->getCode()])['ID'] ?? 0);

        if (!$itemID) {
            return $result->addError(new Error('Error while updating IBLOCK element: No ID'));
        }

        $preparedItem = $this->prepareItem($item);
        if (!$iBlock->Update($itemID, $preparedItem['FIELDS'])) {
            return $result->addError(new Error('Error while updating IBLOCK element: ' . $iBlock->getLastError(), 500, ['ID' => $itemID, 'FIELDS' => $preparedItem['FIELDS']]));
        }

        $this->logger?->debug('Updated fields IBLOCK element: ' . $itemID);

        Debug::dump($preparedItem['PROPERTIES']);

        $iBlock::SetPropertyValuesEx($itemID, $this->getIBlockID(), $preparedItem['PROPERTIES']);
        $this->logger?->debug('Updated properties IBLOCK element: ' . $itemID);

        CIBlock::CleanCache($this->getIBlockID());
        CIBlock::clearIblockTagCache($this->getIBlockID());

        // TODO: Событие после обновления

        return $result->setData($itemID);
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

            // TODO: Добавить транслит кода. Транслит создается на основе настроек ИБ
            $result[$group][$field->getCode()] = $item[$field->getCode()];
        }

        if (!isset($result['FIELDS']['NAME'])) {
            $result['FIELDS']['NAME'] = $item[$this->getKeyField()?->getCode()] ?? '';
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