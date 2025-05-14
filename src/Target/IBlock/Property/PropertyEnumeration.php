<?php

namespace Sholokhov\BitrixExchange\Target\IBlock\Property;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Exception;
use CIBlockPropertyEnum;

use Sholokhov\BitrixExchange\Target\IBlock\IBlock;
use Sholokhov\BitrixExchange\Repository\IBlock\PropertyRepository;

use Sholokhov\BitrixExchange\Helper\Helper;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;
use Sholokhov\BitrixExchange\Messages\Type\Error;
use Sholokhov\BitrixExchange\Target\Attributes\Validate;
use Sholokhov\BitrixExchange\Target\Attributes\BootstrapConfiguration;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Импорт значений списка информационного блока
 *
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
     * Проверка наличия значения списка
     *
     * @param array $item
     * @return bool
     * @throws Exception
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function exists(array $item): bool
    {
        $primaryField = $this->getPrimaryField();

        if ($this->cache->has($item[$primaryField->getCode()])) {
            return true;
        }

        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            'PROPERTY_ID' => $this->getProperty()['ID'],
            $primaryField->getCode() => $item[$primaryField->getCode()],
        ];

        if ($enum = CIBlockPropertyEnum::GetList([], $filter)->Fetch()) {
            $this->cache->set($item[$primaryField->getCode()], (int)$enum['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание значения списка
     *
     * @param array $item
     * @return ResultInterface
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;

        $fields = $this->prepareItem($item);

        $beforeAdd = $this->beforeAdd($fields);
        if (!$beforeAdd->isSuccess()) {
            return $result->addErrors($beforeAdd->getErrors());
        }

        if ($enumId = CIBlockPropertyEnum::Add($fields)) {
            $result->setData((int)$enumId);
            $this->logger?->debug(sprintf('Added the value of the list with the ID "%s"', $enumId));
            $this->cache->set($item[$this->getPrimaryField()->getCode()], (int)$enumId);
        } else {
            $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['ID' => $enumId, 'FIELDS' => $fields, 'RESULT' => $result]))->send();

        return $result;
    }

    /**
     * Обновление значения свойства
     *
     * @param array $item
     * @return ResultInterface
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function update(array $item): ResultInterface
    {
        $result = new DataResult;
        $primaryField = $this->getPrimaryField();

        $enumId = (int)$this->cache->get($item[$primaryField->getCode()]);

        if (!$enumId) {
            return $this->add($item);
        }

        $fields = $this->prepareItem($item);

        $beforeUpdate = $this->beforeUpdate($enumId, $fields);
        if (!$beforeUpdate->isSuccess()) {
            return $result->addErrors($beforeUpdate->getErrors());
        }

        if (!CIBlockPropertyEnum::Update($enumId, $fields)) {
            return $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        $this->logger?->debug(sprintf('Updated the value of the list with the ID "%s"', $enumId));
        $result->setData($enumId);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['FIELDS' => $fields, 'ID' => $enumId, 'RESULT' => $result]))->send();

        return $result;
    }

    /**
     * Преобразование импортируемого значения
     *
     * @param array $item
     * @return array
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function prepareItem(array $item): array
    {
        $result = [];
        $supportedFields = $this->getSupportedFields();

        foreach ($this->getMap() as $field) {
            if (in_array($field->getCode(), $supportedFields)) {
                $result[$field->getCode()] = $item[$field->getCode()];
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
     * Получение информации о свойстве
     *
     * @return array
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function getProperty(): array
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
    private function getPropertyCode(): string
    {
        return $this->getOptions()->get('property_code', '');
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
        $result = new DataResult;

        if (!$this->getPropertyCode()) {
            $result->addError(new Error('Property code is required.'));
        }

        return $result;
    }

    /**
     * Производит проверку возможности импорта данных в свойство
     *
     * @return ResultInterface
     * @throws LoaderException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[Validate]
    private function checkProperty(): ResultInterface
    {
        $result = new DataResult;

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
        $this->repository->set('property_repository', new PropertyRepository($this->getIBlockID()));
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
     * @return ResultInterface
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function beforeUpdate(int $id, array $item): ResultInterface
    {
        $result = new DataResult;

        $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['FIELDS' => &$item, 'ID' => $id]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
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

        return $result;
    }

    /**
     * Событие перед созданием
     *
     * @param array $item
     * @return ResultInterface
     *
     * @version 1.0.0
     * @since 1.0.0
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
                $result->addError(new Error('Error adding IBLOCK list property: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }
}