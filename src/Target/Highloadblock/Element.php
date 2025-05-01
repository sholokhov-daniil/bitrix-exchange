<?php

namespace Sholokhov\BitrixExchange\Target\Highloadblock;

use Bitrix\Main\Diag\Debug;
use Exception;
use ReflectionException;

use Sholokhov\BitrixExchange\Prepares\File\UserField;
use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\ResultInterface;

use Bitrix\Main\Event;
use Bitrix\Main\Loader;
use Bitrix\Main\EventResult;
use Bitrix\Main\SystemException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HLT;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;
use Sholokhov\Exchange\Target\Attributes\Validate;

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
        $keyField = $this->getPrimaryField();

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
        $this->cache->set($item[$this->getPrimaryField()->getCode()], $addResult->getId());

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['ID' => $item, 'FIELDS' => $item, 'RESULT' => $result]))->send();

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
        $keyField = $this->getPrimaryField();

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

        $result->setData($itemID);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['FIELDS' => $item, 'ID' => $itemID, 'RESULT' => $result]))->send();

        return $result;
    }

    /**
     * Конфигурация импорта
     *
     * @return void
     * @throws LoaderException
     * @throws SystemException
     */
    #[BootstrapConfiguration]
    private function bootstrap(): void
    {
        if (Loader::includeModule('highloadblock')) {
            $this->entity = HLT::compileEntity($this->getEntityID())->getDataClass();
        }
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
     * @return ResultInterface
     */
    private function beforeAdd(array $item): ResultInterface
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['FIELDS' => &$item]);
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

        if (!Loader::includeModule('highloadblock')) {
            $result->addError(new Error('Module "highloadblock" not installed'));
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

        if ($this->getEntityID() <= 0) {
            $result->addError(new Error('Entity ID is required'));
        }

        return $result;
    }

    #[BootstrapConfiguration]
    private function bootstrapPrepares(): void
    {
        $entityId = 'HLBLOCK_' . $this->getEntityID();

        $this->getPrepares()
            ->add(new UserField($entityId));
    }
}