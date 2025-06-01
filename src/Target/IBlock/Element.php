<?php

namespace Sholokhov\BitrixExchange\Target\IBlock;

use Bitrix\Main\Diag\Debug;
use Exception;
use CIBlockElement;

use Sholokhov\BitrixExchange\Events\ExchangeEvent;
use Sholokhov\BitrixExchange\Exception\Target\ExchangeItemStoppedException;
use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Helper\Helper;
use Sholokhov\BitrixExchange\Messages\DataResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\Error;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;

use Sholokhov\BitrixExchange\Helper\Site;
use Sholokhov\BitrixExchange\Preparation\IBlock\Element as Prepare;
use Sholokhov\BitrixExchange\Messages\Type\EventResult;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult as BxEventResult;
use Bitrix\Main\Type\DateTime;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Sholokhov\BitrixExchange\Repository\IBlock\PropertyRepository;
use Sholokhov\BitrixExchange\Target\Attributes\BootstrapConfiguration;

/**
 * Импортирование элемента информационного блока
 *
 * @package Target
 * @version 1.0.0
 */
class Element extends IBlock
{
    public const BEFORE_DEACTIVATE = 'onBeforeIBlockElementsDeactivate';
    public const BEFORE_UPDATE_EVENT = 'onBeforeIBlockElementUpdate';
    public const AFTER_UPDATE_EVENT = 'onAfterIBlockElementUpdate';
    public const BEFORE_ADD_EVENT = 'onBeforeIBlockElementAdd';
    public const AFTER_ADD_EVENT = 'onAfterIBlockElementAdd';

    /**
     * Проверка наличия элемента
     *
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();

        if ($this->cache->has($item[$keyField->getTo()])) {
            return true;
        }

        // TODO: Добавлять хэш импорта
        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
        ];

        if ($keyField instanceof ElementFieldInterface) {
            $filter['PROPERTY_' . $keyField->getTo()] = $item[$keyField->getTo()];
        } else {
            $filter[$keyField->getTo()] = $item[$keyField->getTo()];
        }

        if ($element = CIBlockElement::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->set($item[$keyField->getTo()], (int)$element['ID']);

            return true;
        }

        return false;
    }

    /**
     * Добавление элемента в информационный блок
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     */
    protected function add(array $item): DataResultInterface
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

        if ($resultBeforeAdd->isStopped()) {
            return $result;
        }

        if ($itemId = $iblock->Add($data)) {
            // TODO: записываем хэш
            $result->setData((int)$itemId);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $itemId));
            $this->cache->set($item[$this->getPrimaryField()->getTo()], (int)$itemId);
        } else {
            $result->addError(new Error('Error while adding IBLOCK element: ' . strip_tags($iblock->getLastError()), 500, $data));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['id' => $itemId, 'fields' => $data, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Обновление элемента
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     */
    protected function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $keyField = $this->getPrimaryField();

        $iBlock = new CIBlockElement;
        $itemID = $this->cache->get($item[$keyField->getTo()]);

        if (!$itemID) {
            return $this->add($item);
        }

        $preparedItem = $this->prepareItem($item);
        if (!isset($preparedItem['FIELDS']['ACTIVE'])) {
            $preparedItem['FIELDS']['ACTIVE'] = 'Y';
        }

        $resultBeforeUpdate = $this->beforeUpdate($itemID, $preparedItem);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        if ($resultBeforeUpdate->isSuccess()) {
            return $result;
        }

        if (!$iBlock->Update($itemID, $preparedItem['FIELDS'])) {
            return $result->addError(new Error('Error while updating IBLOCK element: ' . $iBlock->getLastError(), 500, ['ID' => $itemID, 'FIELDS' => $preparedItem['FIELDS']]));
        }

        $this->logger?->debug('Updated fields IBLOCK element: ' . $itemID);

        $iBlock::SetPropertyValuesEx($itemID, $this->getIBlockID(), $preparedItem['PROPERTIES']);

        $this->logger?->debug('Updated properties IBLOCK element: ' . $itemID);
        $this->cleanCache();

        $result->setData((int)$itemID);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['fields' => $preparedItem, 'id' => $itemID, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Разделение импортируемых данных на группы
     *
     * @param array{FIELDS: array, PROPERTIES: array} $item
     * @return array|array[]
     * @throws Exception
     *
     * @version 1.0.0
     */
    protected function prepareItem(array $item): array
    {
        $result = [
            'FIELDS' => [],
            'PROPERTIES' => []
        ];

        foreach ($this->getMap() as $field) {
            $group = 'FIELDS';
            $value = $item[$field->getTo()] ?? null;

            if ($field instanceof ElementFieldInterface) {
                $group = 'PROPERTIES';
            } elseif ($field->getTo() === 'CODE') {
                $translitOptions = $this->getIBlockInfo()->get('FIELDS')['CODE']['DEFAULT_VALUE'] ?? [];

                if ($translitOptions) {
                    $value = \CUtil::translit($value, Site::getLanguage(), $translitOptions);
                }
            }

            $result[$group][$field->getTo()] = $value;
        }

        $requiredFields = ['NAME', 'CODE', 'XML_ID'];
        array_walk($requiredFields, function($field) use (&$result, $item) {
            if (!isset($result['FIELDS'][$field])) {
                $result['FIELDS'][$field] = $item[$this->getPrimaryField()?->getTo()] ?? '';
            }
        });

        return $result;
    }

    /**
     * Деактивация элементов, которые не пришли в импорте
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

        (new Event(Helper::getModuleID(), self::BEFORE_DEACTIVATE, ['parameters' => &$parameters]))->send();

        $iBlock = new CIBlockElement;
        $iterator = ElementTable::getList($parameters);

        while ($element = $iterator->fetch()) {
            $iBlock->Update($element['ID'], ['ACTIVE' => 'N']);
        }
    }

    /**
     * Событие перед обновлением элемента
     *
     * @param int $id
     * @param array $item
     * @return EventResult
     */
    private function beforeUpdate(int $id, array &$item): EventResult
    {
        $result = new EventResult;

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['fields' => &$item['FIELDS'], 'id' => $id, 'exchange' => $this]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BxEventResult::SUCCESS) {
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
        } catch (ExchangeItemStoppedException $exception) {
            $this->logger?->warning($exception->getMessage() ?: 'The update of the iblock element has been stopped');
            $result->setStopped();
        }

        return $result;
    }

    /**
     * Событие перед созданием элемента
     *
     * @param array $item
     * @return EventResult
     */
    private function beforeAdd(array $item): EventResult
    {
        $result = new EventResult();

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['fields' => &$item]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BxEventResult::SUCCESS) {
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
        } catch (ExchangeItemStoppedException $exception) {
            $this->logger?->warning($exception->getMessage() ?: 'The adding of the iblock element has been stopped');
            $result->setStopped();

        }

        return $result;
    }

    /**
     * Проверка, что поле является множественным
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function isMultipleField(FieldInterface $field): bool
    {
        $property = $this->getPropertyRepository()->get($field->getTo());
        return $property && $property['MULTIPLE'] === 'Y';
    }

    /**
     * Получение хранилища свойств ИБ
     *
     * @return PropertyRepository
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getPropertyRepository(): PropertyRepository
    {
        return $this->repository->get('property_repository');
    }

    /**
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapPrepares(): void
    {
        $iBlockID = $this->getIBlockID();
        $this->addPrepared(new Prepare\Date($iBlockID))
            ->addPrepared(new Prepare\DateTime($iBlockID))
            ->addPrepared(new Prepare\Number($iBlockID))
            ->addPrepared(new Prepare\Enumeration($iBlockID))
            ->addPrepared(new Prepare\PropertyFile($iBlockID))
            ->addPrepared(new Prepare\IBlockElement($iBlockID))
            ->addPrepared(new Prepare\File)
            ->addPrepared(new Prepare\IBlockSection($iBlockID))
            ->addPrepared(new Prepare\HtmlText($iBlockID))
            ->addPrepared(new Prepare\HandbookElement($iBlockID));
        // Video
        // Деньги
        // Привязка к яндекс.карте
        // Привязка к Google.Maps
        // Привязка к пользователю
        // Привязка к разделам автозаполнения
        // Привязка к теме форума
        // Привязка к товару(SKU)
        // Привязка к файлу (на сервере)
        // Привязка к элементам в виде списка
        // Привязка к элементам по XML_ID
        // Привязка к элементам с автозаполнением
        // Счетчик
    }

    /**
     * Конфигурация связанных объектов обмена
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[BootstrapConfiguration]
    private function configuration(): void
    {
        $this->repository->set('property_repository', new PropertyRepository(['iblock_id' => $this->getIBlockID()]));
    }
}
