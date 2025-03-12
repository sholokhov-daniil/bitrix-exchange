<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CIBlockElement;

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Helper\Site;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Fields\IBlock\ElementField;

use Bitrix\Main\Event;
use Bitrix\Main\Error;
use Bitrix\Main\EventResult;
use Bitrix\Main\Type\DateTime;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;

use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Импортирование элемента информационного блока
 */
class Element extends IBlock
{
    public const BEFORE_DEACTIVATE = 'onBeforeIBlockElementsDeactivate';
    public const BEFORE_UPDATE_EVENT = 'onBeforeIBlockElementUpdate';
    public const AFTER_UPDATE_EVENT = 'onAfterIBlockElementUpdate';
    public const BEFORE_ADD_EVENT = 'onBeforeIBlockElementAdd';
    public const AFTER_ADD_EVENT = 'onAfterIBlockElementAdd';

    /**
     * Обработка параметров импорта
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        if (!isset($options['deactivate']) || !is_bool($options['deactivate'])) {
            $options['deactivate'] = false;
        }

        return parent::normalizeOptions($options);
    }

    protected function configure(): void
    {
        $this->event->subscribeBeforeRun([$this, 'deactivate']);
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
        ];

        if ($keyField instanceof ElementField) {
            $filter['PROPERTY_' . $keyField->getCode()] = $item[$keyField->getCode()];
        } else {
            $filter[$keyField->getCode()] = $item[$keyField->getCode()];
        }

        if ($element = CIBlockElement::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->set($item[$keyField->getCode()], (int)$element['ID']);

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

        $resultBeforeAdd = $this->beforeAdd($data);
        if (!$resultBeforeAdd->isSuccess()) {
            return $result->addErrors($resultBeforeAdd->getErrors());
        }

        $itemId = $iblock->Add($data);

        if ($itemId) {
            // TODO: записываем хэш
            $result->setData((int)$itemId);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $itemId));

            if ($keyField = $this->getKeyField()) {
                $this->cache->set($item[$keyField->getCode()], (int)$itemId);
            }
        } else {
            $result->addError(new Error('Error while adding IBLOCK element: ' . $iblock->getLastError(), 500, $data));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['ID' => $itemId, 'FIELDS' => $data,]))->send();

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

        $keyField = $this->getKeyField();

        if (!$keyField) {
            return $result->addError(new Error('Error while updating IBLOCK element: No identification field'));
        }

        $iBlock = new CIBlockElement;
        $itemID = $this->cache->get($item[$keyField->getCode()]);

        if (!$itemID) {
            return $result->addError(new Error('Error while updating IBLOCK element: No ID'));
        }

        $preparedItem = $this->prepareItem($item);
        if (!isset($preparedItem['FIELDS']['ACTIVE'])) {
            $preparedItem['FIELDS']['ACTIVE'] = 'Y';
        }

        $resultBeforeUpdate = $this->beforeUpdate($preparedItem);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        if (!$iBlock->Update($itemID, $preparedItem['FIELDS'])) {
            return $result->addError(new Error('Error while updating IBLOCK element: ' . $iBlock->getLastError(), 500, ['ID' => $itemID, 'FIELDS' => $preparedItem['FIELDS']]));
        }

        $this->logger?->debug('Updated fields IBLOCK element: ' . $itemID);

        $iBlock::SetPropertyValuesEx($itemID, $this->getIBlockID(), $preparedItem['PROPERTIES']);
        $this->logger?->debug('Updated properties IBLOCK element: ' . $itemID);

        $this->cleanCache();

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, $preparedItem))->send();

        return $result->setData($itemID);
    }

    /**
     * Разделение импортируемых данных на группы
     *
     * @param array{FIELDS: array, PROPERTIES: array} $item
     * @return array|array[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function prepareItem(array $item): array
    {
        $result = [
            'FIELDS' => [],
            'PROPERTIES' => []
        ];

        foreach ($this->getMap() as $field) {
            $group = 'FIELDS';
            $value = $item[$field->getCode()];

            if ($field instanceof ElementField && $field->isProperty()) {
                $group = 'PROPERTIES';
            } elseif ($field->getCode() === 'CODE') {
                $translitOptions = $this->getIBlockInfo()['FIELDS']['CODE']['DEFAULT_VALUE'] ?? [];

                if ($translitOptions) {
                    $value = \CUtil::translit($value, Site::getLanguage(), $translitOptions);
                }
            }

            $result[$group][$field->getCode()] = $value;
        }

        if (!isset($result['FIELDS']['NAME'])) {
            $result['FIELDS']['NAME'] = $item[$this->getKeyField()?->getCode()] ?? '';
        }

        return $result;
    }

    /**
     * Деактивация элементов, которые не пришли в импорте
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function deactivate(): void
    {
        if (!$this->getOptions()->get('deactivate')) {
            return;
        }

        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            '<TIMESTAMP_X' => DateTime::createFromTimestamp($this->dateUp),
            'ACTIVE' => 'Y',
        ];
        $select = ['ID'];

        $parameters = compact('filter', 'select');

        (new Event(Helper::getModuleID(), self::BEFORE_DEACTIVATE, ['PARAMETERS' => &$parameters]))->send();

        $iBlock = new CIBlockElement;
        $iterator = ElementTable::getList($parameters);

        while ($element = $iterator->fetch()) {
            $iBlock->Update($element['ID'], ['ACTIVE' => 'N']);
        }
    }

    /**
     * Событие перед обновлением элемента
     *
     * @param array $item
     * @return Result
     */
    private function beforeUpdate(array &$item): Result
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['FIELDS' => &$item['FIELDS']]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                continue;
            }

            $parameters = $eventResult->getParameters();
            if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                $result->addError(new Error('Error while updating IBLOCK element: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }

    /**
     * Событие перед созданием элемента
     *
     * @param array $item
     * @return Result
     */
    private function beforeAdd(array $item): Result
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['ITEM' => &$item]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                continue;
            }

            $parameters = $eventResult->getParameters();
            if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                $result->addError(new Error('Error while adding IBLOCK element: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }
}