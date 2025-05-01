<?php

namespace Sholokhov\BitrixExchange\Target\IBlock\Property;

use Exception;
use CIBlockPropertyEnum;

use Sholokhov\BitrixExchange\Target\IBlock\IBlock;
use Sholokhov\BitrixExchange\Repository\IBlock\PropertyRepository;

use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Target\Attributes\Validate;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\LoaderException;

/**
 * Импорт значений списка информационного блока
 */
class PropertyEnumeration extends IBlock
{
    /**
     * Проверка наличия значения списка
     *
     * @param array $item
     * @return bool
     * @throws Exception
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
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;

        // TODO: Добавить событие

        $fields = $this->prepareItem($item);

        if ($enumId = CIBlockPropertyEnum::Add($fields)) {
            $result->setData((int)$enumId);
            $this->logger?->debug(sprintf('Added the value of the list with the ID "%s"', $enumId));
            $this->cache->set($item[$this->getPrimaryField()->getCode()], (int)$enumId);
        } else {
            $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        // TODO: Добавить событие

        return $result;
    }

    /**
     * Обновление значения свойства
     *
     * @param array $item
     * @return ResultInterface
     * @throws LoaderException
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

        // TODO: Add event
        if (!CIBlockPropertyEnum::Update($enumId, $fields)) {
            return $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        $this->logger?->debug(sprintf('Updated the value of the list with the ID "%s"', $enumId));
        $result->setData($enumId);

        // TODO: add event

        return $result;
    }

    /**
     * Преобразование импортируемого значения
     *
     * @param array $item
     * @return array
     * @throws LoaderException
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

    private function getSupportedFields(): array
    {
        return ['VALUE', 'ID', 'SORT', 'DEF', 'XML_ID', 'EXTERNAL_ID'];
    }

    /**
     * Получение информации о свойстве
     *
     * @return array
     * @throws LoaderException
     */
    private function getProperty(): array
    {
        return $this->getPropertyRepository()->get($this->getPropertyCode(), []);
    }

    /**
     * Получение кода свойства в которое производится импорт данных
     *
     * @return string
     */
    private function getPropertyCode(): string
    {
        return $this->getOptions()->get('property_code', '');
    }

    /**
     * Проверка валидности конфигурации импорта
     *
     * @return ResultInterface
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
     */
    private function getPropertyRepository(): PropertyRepository
    {
        return $this->repository->get('property_repository');
    }
}