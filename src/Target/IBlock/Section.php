<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CUtil;
use Exception;
use CIBlockSection;

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Helper\Site;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;

use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\ArgumentException;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

/**
 * Импорт разделов информационного блока
 */
class Section extends IBlock
{
    public const BEFORE_DEACTIVATE = 'onBeforeIBlockSectionsDeactivate';
    public const BEFORE_UPDATE_EVENT = 'onBeforeIBlockSectionUpdate';
    public const AFTER_UPDATE_EVENT = 'onAfterIBlockSectionUpdate';
    public const BEFORE_ADD_EVENT = 'onBeforeIBlockSectionAdd';
    public const AFTER_ADD_EVENT = 'onAfterIBlockSectionAdd';

    /**
     * Проверка наличия раздела
     *
     * @param array $item
     * @return bool
     * @throws Exception
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

        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            $keyField->getCode() => $item[$keyField->getCode()],
        ];

        if ($section = CIBlockSection::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->set($item[$keyField->getCode()], (int)$section['ID']);
            return true;
        }

        return false;
    }

    /**
     * Добавление раздела
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;
        $section = new CIBlockSection;
        $fields = $this->prepareItem($item);

        $resultBeforeAdd = $this->beforeAdd($fields);
        if (!$resultBeforeAdd->isSuccess()) {
            return $result->addErrors($resultBeforeAdd->getErrors());
        }

        if ($id = $section->Add($fields)) {
            $result->setData((int)$id);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $id));

            if ($keyField = $this->getKeyField()) {
                $this->cache->set($item[$keyField->getCode()], (int)$id);
            }
        } else {
            $result->addError(new Error('Error while adding IBLOCK section: ' . strip_tags($section->getLastError()), 500, $fields));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['ID' => $id, 'FIELDS' => $fields]))->send();

        return $result;
    }

    /**
     * Обновление раздела
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function update(array $item): ResultInterface
    {
        $result = new DataResult;
        $keyField = $this->getKeyField();

        $section = new CIBlockSection;
        $sectionId = $this->cache->get($item[$keyField->getCode()]);

        if (!$sectionId) {
            return $this->add($item);
        }

        $result->setData((int)$sectionId);

        $preparedItem = $this->prepareItem($item);
        if (!isset($preparedItem['ACTIVE'])) {
            $preparedItem['ACTIVE'] = 'Y';
        }

        $resultBeforeUpdate = $this->beforeUpdate($preparedItem);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        if (!$section->Update($sectionId, $preparedItem)) {
            return $result->addError(new Error('Error while updating IBLOCK section: ' . $section->getLastError(), 500, ['ID' => $sectionId, 'FIELDS' => $preparedItem]));
        }

        $this->logger?->debug('Updated properties IBLOCK section: ' . $sectionId);
        $this->cleanCache();

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, $preparedItem))->send();

        return $result;
    }

    /**
     * Преобразование данных, которые поддерживаются разделами
     *
     * @param array $item
     * @return array
     * @throws Exception
     */
    protected function prepareItem(array $item): array
    {
        $result = [];
        $translitOptions = $this->getIBlockInfo()['FIELDS']['CODE']['DEFAULT_VALUE'] ?? [];

        foreach ($this->getMap() as $field) {
            $value = $item[$field->getCode()] ?? '';

            if ($field->getCode() === 'CODE' && $translitOptions) {
                $value = CUtil::translit($value, Site::getLanguage(), $translitOptions);
            }

            $result[$field->getCode()] = $value;
        }

        if (!isset($result['NAME'])) {
            $result['NAME'] = $item[$this->getKeyField()?->getCode()] ?? '';
        }

        if (!isset($result['CODE'])) {
            $result['CODE'] = CUtil::translit($result['NAME'], Site::getLanguage(), $translitOptions);
        }

        $result['IBLOCK_ID'] = $this->getIBlockID();

        return $result;
    }

    /**
     * Деактивация разделов, которые не пришли в импорте
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function deactivate(): void
    {
        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            '<TIMESTAMP_X' => DateTime::createFromTimestamp($this->dateUp),
            'ACTIVE' => 'Y',
        ];
        $select = ['ID'];

        $parameters = compact('filter', 'select');

        (new Event(Helper::getModuleID(), self::BEFORE_DEACTIVATE, ['PARAMETERS' => &$parameters]))->send();

        $iterator = SectionTable::getList($parameters);
        while ($section = $iterator->fetch()) {
            SectionTable::update($section['ID'], ['ACTIVE' => 'N']);
        }
    }

    /**
     * Событие перед обновлением раздела
     *
     * @param array $item
     * @return ResultInterface
     */
    private function beforeUpdate(array &$item): ResultInterface
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['FIELDS' => &$item]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                continue;
            }

            $parameters = $eventResult->getParameters();
            if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                $result->addError(new Error('Error while updating IBLOCK section: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }

    /**
     * Событие перед созданием раздела
     *
     * @param array $item
     * @return ResultInterface
     */
    private function beforeAdd(array $item): ResultInterface
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
                $result->addError(new Error('Error while adding IBLOCK section: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }
}