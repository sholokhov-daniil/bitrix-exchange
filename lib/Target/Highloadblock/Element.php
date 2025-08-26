<?php

namespace Sholokhov\Exchange\Target\Highloadblock;

use Exception;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\EventResult;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Preparation\UserField as Prepare;
use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Repository\Fields\FieldRepositoryInterface;
use Sholokhov\Exchange\Repository\Fields\UFRepository;
use Sholokhov\Exchange\Target\Attributes\Configuration;
use Sholokhov\Exchange\Target\Attributes\Validate;

use Sholokhov\Exchange\Messages\Type\Error;
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
use Bitrix\Main\EventResult as BXEventResult;
use Bitrix\Main\SystemException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HLT;

/**
 * @package Target
 */
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
     * @since 1.0.0
     * @version 1.0.1
     */
    public function getHlID(): int
    {
        return $this->getOptions()->get('entity_id') ?: 0;
    }

    /**
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getUfEntityID(): string
    {
        $options = $this->getOptions();
        if (!$options->has('uf_entity_id')) {
           $options->set('uf_entity_id', HLT::compileEntityId($this->getHlID()));
        }

        return $options->get('uf_entity_id');
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
    public function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();

        if ($this->cache->has($item[$keyField->getTo()])) {
            return true;
        }

        // TODO: Добавить хэш импорта
        $filter = [
            $keyField->getTo() => $item[$keyField->getTo()],
        ];

        $select = [$keyField->getTo(), 'ID'];
        $element = $this->entity::getRow(compact('filter', 'select'));

        if ($element) {
            $this->cache->set($item[$keyField->getTo()], (int)$element['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание элемента сущности
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     */
    public function add(array $item): DataResultInterface
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
                $this->getHlID(),
                implode('. ', $addResult->getErrorMessages())
            );
            $this->logger?->critical($errorMessage);

            return $result->addError(new Error($errorMessage, 500, $item));
        }

        $result->setData($addResult->getId());
        $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the "%s" highloadblock', $addResult->getId(), $this->getHlID()));
        $this->cache->set($item[$this->getPrimaryField()->getTo()], $addResult->getId());

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['id' => $item, 'fields' => $item, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return DataResult
     * @throws Exception
     */
    public function update(array $item): DataResult
    {
        $result = new DataResult;
        $keyField = $this->getPrimaryField();

        $itemID = $this->cache->get($item[$keyField->getTo()]);

        if (!$itemID) {
            return $this->add($item);
        }

        $resultBeforeUpdate = $this->beforeUpdate($itemID, $item);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        if ($resultBeforeUpdate->isStopped()) {
            return $result;
        }

        $updateResult = $this->entity::update($itemID, $item);

        if (!$updateResult->isSuccess()) {
            $errorMessage = sprintf(
                'An error occurred when updating an element with the ID "%s" to the highloadblock "%s": %s',
                $itemID,
                $this->getHlID(),
                implode('. ', $updateResult->getErrorMessages())
            );
            $this->logger?->critical($errorMessage);

            return $result->addError(new Error($errorMessage, 500, ['id' => $itemID, 'fields' => $item]));
        }

        $this->logger?->debug(
            sprintf(
                'The element with the ID "%s" in the highloadblock "%s" has been successfully updated.',
                $itemID,
                $this->getHlID()
            )
        );

        $result->setData($itemID);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['fields' => $item, 'id' => $itemID, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isMultipleField(FieldInterface $field): bool
    {
        $property = $this->getUfRepository()->get($field->getTo());
        return $property && $property['MULTIPLE'] === 'Y';
    }

    /**
     * Получение хранилища пользовательских свойств (UF)
     *
     * @return FieldRepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getUfRepository(): FieldRepositoryInterface
    {
        return $this->repository->get('uf_repository');
    }

    /**
     * Конфигурация импорта
     *
     * @return void
     * @throws LoaderException
     * @throws SystemException
     */
    #[Configuration]
    private function bootstrap(): void
    {
        if (!Loader::includeModule('highloadblock')) {
            throw new LoaderException('Module "highloadblock" not installed');
        }

        $entity = HLT::compileEntity($this->getHlID());

        if (!$entity) {
            throw new LoaderException('Entity "' . $this->getHlID() . '" not found');
        }

        $this->entity = $entity->getDataClass();
        $this->repository->set('uf_repository', new UFRepository(['entity_id' => $this->getUfEntityID()]));
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
        $result = new EventResult();

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['fields' => &$item['FIELDS'], 'id' => $id, 'exchange' => $this,]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BXEventResult::SUCCESS) {
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
        } catch (Exception $ex) {
            $this->logger?->warning(($ex->getMessage() ?: 'An error occurred while updating IBLOCK element') . ': ' . $id);
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
            $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['fields' => &$item, 'exchange' => $this]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BXEventResult::SUCCESS) {
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
        } catch (Exception $ex) {
            $this->logger?->warning(($ex->getMessage() ?: 'An error occurred while adding highloadblock element') . ': ' . json_encode($item));
            $result->setStopped();
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

        if ($this->getHlID() <= 0) {
            $result->addError(new Error('Entity ID is required'));
        }

        return $result;
    }

    /**
     * Инициализация преобразователей импортированных значений
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[Configuration]
    private function bootstrapPrepares(): void
    {
        $entityId = $this->getUfEntityID();

        $this->addPrepared(new Prepare\File($entityId))
            ->addPrepared(new Prepare\Date($entityId))
            ->addPrepared(new Prepare\DateTime($entityId))
            ->addPrepared(new Prepare\Boolean($entityId))
            ->addPrepared(new Prepare\IBlockElement($entityId))
            ->addPrepared(new Prepare\IBlockSection($entityId))
            ->addPrepared(new Prepare\Enumeration($entityId));

        // Адрес
        // Видео
        // Деньги
        // Опрос
        // Привязка к элементам справочника
        // Содержимое ссылки
        // Ссылка
        // Целое число
        // Число
        // Шаблон
    }
}