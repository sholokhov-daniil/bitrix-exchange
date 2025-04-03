<?php

namespace Sholokhov\Exchange\Target\Highloadblock;

use Exception;
use ReflectionException;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\ResultInterface;

use Bitrix\Main\Event;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\EventResult;
use Bitrix\Main\SystemException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HLT;

class Element extends Exchange
{
    public const BEFORE_UPDATE_EVENT = 'onBeforeHighloadblockElementUpdate';
    public const AFTER_UPDATE_EVENT = 'onAfterHighloadblockElementUpdate';
    public const BEFORE_ADD_EVENT = 'onBeforeHighloadblockElementAdd';
    public const AFTER_ADD_EVENT = 'onAfterHighloadblockElementAdd';

    protected readonly DataManager|string $entity;

    /**
     * Получение ID справочника
     *
     * @return int
     */
    public function getEntityID(): int
    {
        return $this->getOptions()->get('entity_id') ?: 0;
    }

    /**
     * Предварительная обработка конфигураций импорта
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        $options['entity_id'] = (int)$options['entity_id'];
        return parent::normalizeOptions($options);
    }

    /**
     * Конфигурация импорта
     *
     * @return void
     * @throws LoaderException
     * @throws SystemException
     */
    protected function configure(): void
    {
        if (Loader::includeModule('highloadblock')) {
            $this->entity = HLT::compileEntity($this->getEntityID())->getDataClass();
        }
    }

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

        if (!Loader::includeModule('highloadblock')) {
            $result->addError(new Error('Module "highloadblock" not installed'));
        }

        if ($this->getEntityID() <= 0) {
            $result->addError(new Error('Entity ID is required'));
        }

        return $result;
    }

    /**
     * Проверка наличия элемента сущности
     *
     * @param array $item
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getKeyField();

        if ($this->cache->has($item[$keyField->getCode()])) {
            return true;
        }

        // TODO: Добавить хэш импорта
        $filter = [
            $keyField->getCode() => $item[$keyField->getCode()],
        ];

        $select = [$keyField->getCode(), 'ID'];
        $element = $this->entity::getRow(compact('filter', 'select'));

        if ($element) {
            $this->cache->set($item[$keyField->getCode()], (int)$element['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание элемента сущности
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;

        $resultBeforeAdd = $this->beforeAdd($item);
        if (!$resultBeforeAdd->isSuccess()) {
            return $result->addErrors($resultBeforeAdd->getErrors());
        }

        $addResult = $this->entity::add($item);

        if (!$addResult->isSuccess()) {
            $errorMessage = sprintf(
                'An error occurred when adding an element to the highloadblock "%s": %s',
                $this->getEntityID(),
                implode('. ', $addResult->getErrorMessages())
            );
            $this->logger?->critical($errorMessage);

            return $result->addError(new Error($errorMessage, 500, $item));
        }

        $result->setData($addResult->getId());
        $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the "%s" highloadblock', $addResult->getId(), $this->getEntityID()));
        $this->cache->set($item[$this->getKeyField()->getCode()], $addResult->getId());

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['id' => $item, 'fields' => $item]))->send();

        return $result;
    }

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function update(array $item): ResultInterface
    {
        $result = new DataResult;
        $keyField = $this->getKeyField();

        $itemID = $this->cache->get($item[$keyField->getCode()]);

        if (!$itemID) {
            return $this->add($item);
        }

        $resultBeforeUpdate = $this->beforeUpdate($item);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        $updateResult = $this->entity::update($itemID, $item);

        if (!$updateResult->isSuccess()) {
            $errorMessage = sprintf(
                'An error occurred when updating an element with the ID "%s" to the highloadblock "%s": %s',
                $itemID,
                $this->getEntityID(),
                implode('. ', $updateResult->getErrorMessages())
            );
            $this->logger?->critical($errorMessage);

            return $result->addError(new Error($errorMessage, 500, ['id' => $itemID, 'fields' => $item]));
        }

        $this->logger?->debug(
            sprintf(
                'The element with the ID "%s" in the highloadblock "%s" has been successfully updated.',
                $itemID,
                $this->getEntityID()
            )
        );

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['item' => $item, 'id' => $itemID]))->send();

        return $result->setData($itemID);
    }

    /**
     * Событие перед обновлением элемента
     *
     * @param array $item
     * @return ResultInterface
     */
    private function beforeUpdate(array &$item): ResultInterface
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['fields' => &$item['FIELDS']]);
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
     * @return ResultInterface
     */
    private function beforeAdd(array $item): ResultInterface
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['item' => &$item]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                continue;
            }

            $parameters = $eventResult->getParameters();
            if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                $result->addError(new Error('Error while adding Highloadblock element: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }
}