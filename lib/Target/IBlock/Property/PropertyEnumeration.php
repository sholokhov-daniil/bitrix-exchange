<?php

namespace Sholokhov\Exchange\Target\IBlock\Property;

use Throwable;
use Exception;
use CIBlockPropertyEnum;

use Sholokhov\Exchange\Exception\Target\ExchangeItemStoppedException;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\EventResult;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Target\IBlock\IBlock;
use Sholokhov\Exchange\Repository\IBlock\PropertyRepository;

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Target\Attributes\Validate;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult as BXEventResult;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Импорт значений списка информационного блока
 *
 * @package Target
 * @version 1.0.0
 * @since 1.0.0
 */
class PropertyEnumeration extends IBlock
{
    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const BEFORE_UPDATE_EVENT = 'onBeforeIBlockPropertyEnumerationUpdate';

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const AFTER_UPDATE_EVENT = 'onAfterIBlockPropertyEnumerationUpdate';

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const BEFORE_ADD_EVENT = 'onBeforeIBlockPropertyEnumerationAdd';

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const AFTER_ADD_EVENT = 'onAfterIBlockPropertyEnumerationAdd';

    /**
     * Получение информации о свойстве
     *
     * @return array
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getProperty(): array
    {
        return $this->getPropertyRepository()->get($this->getPropertyCode(), []);
    }

    /**
     * Получение кода свойства в которое производится импорт данных
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getPropertyCode(): string
    {
        return $this->getOptions()->get('property_code', '');
    }

    /**
     * Проверка наличия значения списка
     *
     * @param array $item
     * @return bool
     * @throws Exception
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function exists(array $item): bool
    {
        $primaryField = $this->getPrimaryField();

        if ($this->cache->has($item[$primaryField->getTo()])) {
            return true;
        }

        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            'PROPERTY_ID' => $this->getProperty()['ID'],
            $primaryField->getTo() => $item[$primaryField->getTo()],
        ];

        if ($enum = CIBlockPropertyEnum::GetList([], $filter)->Fetch()) {
            $this->cache->set($item[$primaryField->getTo()], (int)$enum['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание значения списка
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     * @version 1.0.0
     * @since 1.0.0
     */
    public function add(array $item): DataResultInterface
    {
        $result = new DataResult;

        $fields = $this->prepareItem($item);

        $beforeAdd = $this->beforeAdd($fields);
        if (!$beforeAdd->isSuccess()) {
            return $result->addErrors($beforeAdd->getErrors());
        }

        if ($beforeAdd->isStopped()) {
            return $result;
        }

        if ($enumId = CIBlockPropertyEnum::Add($fields)) {
            $result->setData((int)$enumId);
            $this->logger?->debug(sprintf('Added the value of the list with the ID "%s"', $enumId));
            $this->cache->set($item[$this->getPrimaryField()->getTo()], (int)$enumId);
        } else {
            $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['id' => $enumId, 'fields' => $fields, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Обновление значения свойства
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     * @version 1.0.0
     * @since 1.0.0
     */
    public function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $primaryField = $this->getPrimaryField();

        $enumId = (int)$this->cache->get($item[$primaryField->getTo()]);

        if (!$enumId) {
            return $this->add($item);
        }

        $fields = $this->prepareItem($item);

        $beforeUpdate = $this->beforeUpdate($enumId, $fields);
        if (!$beforeUpdate->isSuccess()) {
            return $result->addErrors($beforeUpdate->getErrors());
        }

        if ($beforeUpdate->isStopped()) {
            return $result;
        }

        if (!CIBlockPropertyEnum::Update($enumId, $fields)) {
            return $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        $this->logger?->debug(sprintf('Updated the value of the list with the ID "%s"', $enumId));
        $result->setData($enumId);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['fields' => $fields, 'id' => $enumId, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Проверка на множественный тип свойства
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isMultipleField(FieldInterface $field): bool
    {
        $repository = $this->getPropertyRepository()->get($field->getTo());
        return $repository && $repository['MULTIPLE'] === 'Y';
    }

    /**
     * Преобразование импортируемого значения
     *
     * @param array $item
     * @return array
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function prepareItem(array $item): array
    {
        $result = [];
        $supportedFields = $this->getSupportedFields();

        foreach ($this->getMap() as $field) {
            if (in_array($field->getTo(), $supportedFields)) {
                $result[$field->getTo()] = $item[$field->getTo()];
            }
        }

        $result['PROPERTY_ID'] = $this->getProperty()['ID'];
        $result['IBLOCK_ID'] = $this->getIBlockID();

        return $result;
    }

    /**
     * Список поддерживаемых полей значения свойства, для импортирования
     *
     * @return string[]
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function getSupportedFields(): array
    {
        return ['VALUE', 'ID', 'SORT', 'DEF', 'XML_ID', 'EXTERNAL_ID'];
    }

    /**
     * Проверка валидности конфигурации импорта
     *
     * @return ResultInterface
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[Validate]
    private function optionsValidate(): ResultInterface
    {
        $result = new Result;

        if (!$this->getPropertyCode()) {
            $result->addError(new Error('Property code is required.'));
        }

        return $result;
    }

    /**
     * Производит проверку возможности импорта данных в свойство
     *
     * @return ResultInterface
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[Validate]
    private function checkProperty(): ResultInterface
    {
        $result = new Result;

        $property = $this->getPropertyRepository()->get($this->getPropertyCode());

        if (!$property) {
            return $result->addError(new Error('Property not found'));
        }

        if ($property['PROPERTY_TYPE'] <> PropertyTable::TYPE_LIST || $property['USER_TYPE']) {
            $result->addError(new Error('Invalid property data type'));
        }

        return $result;
    }

    /**
     * Инициализация хранилища свойств информационного блока
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapPropertyRepository(): void
    {
        $this->repository->set('property_repository', new PropertyRepository([
            'iblock_id' => $this->getIBlockID(),
        ]));
    }

    /**
     * Получение хранилища данных свойств информационного блока
     *
     * @return PropertyRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function getPropertyRepository(): PropertyRepository
    {
        return $this->repository->get('property_repository');
    }

    /**
     * Событие перед обновлением
     *
     * @param int $id
     * @param array $item
     * @return EventResult
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function beforeUpdate(int $id, array $item): EventResult
    {
        $result = new EventResult;

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['fields' => &$item, 'id' => $id]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BXEventResult::SUCCESS) {
                    continue;
                }

                $parameters = $eventResult->getParameters();
                if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                    $result->addError(new Error('Error updating IBLOCK list property: stopped', 300, $item));
                } else {
                    foreach ($parameters['ERRORS'] as $error) {
                        $result->addError(new Error($error, 300, $item));
                    }
                }
            }
        } catch (ExchangeItemStoppedException $exception) {
            $stoppedMessage = $exception->getMessage() ?: ('Updating of the iblock property has been stopped: ' . json_encode($item));
            $this->logger?->warning($stoppedMessage);
            $result->setStopped();
        }

        return $result;
    }

    /**
     * Событие перед созданием
     *
     * @param array $item
     * @return EventResult
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function beforeAdd(array $item): EventResult
    {
        $result = new EventResult;

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['fields' => &$item]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BXEventResult::SUCCESS) {
                    continue;
                }

                $parameters = $eventResult->getParameters();
                if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                    $result->addError(new Error('Error adding IBLOCK list property: stopped', 300, $item));
                } else {
                    foreach ($parameters['ERRORS'] as $error) {
                        $result->addError(new Error($error, 300, $item));
                    }
                }
            }
        } catch (Throwable $throwable) {
            $stoppedMessage = $throwable->getMessage() ?: ('Adding of the iblock property has been stopped: ' . json_encode($item));
            $this->logger?->warning($stoppedMessage);
            $result->setStopped();
        }

        return $result;
    }
}