<?php

namespace Sholokhov\Exchange\Target\Sale;

use Bitrix\Catalog\StoreTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CUserTypeEntity;
use Exception;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\Type\EventResult;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Target\Attributes\Validate;

class Warehouse extends AbstractExchange
{
    /**
     * Проверка наличия склада
     *
     * @param array $item
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     *
     * @version 1.1.1
     * @since 1.1.1
     */
    public function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();
        $externalId = $item[$keyField->getTo()];

        if ($this->cache->has($externalId)) {
            return true;
        }

        $select = ['ID'];
        $filter = [$keyField->getTo() => $externalId];
        $store = StoreTable::getRow(compact('filter', 'select'));

        if ($store) {
            $this->cache->set($externalId, (int)$store['ID']);
            return true;
        }

        return false;
    }

    /**
     * Логика создания склада
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     * @version 1.1.1
     * @since 1.1.1
     */
    public function add(array $item): DataResultInterface
    {
        $result = new DataResult;
        $fields = $this->preparation($item);

        $beforeAdd = $this->beforeAdd($fields);
        if (!$beforeAdd->isSuccess()) {
            return $result->addErrors($beforeAdd->getErrors());
        }

        if ($beforeAdd->isStopped()) {
            return $result;
        }

        $addResult = StoreTable::add($fields);
        if (!$addResult->isSuccess()) {
            return $result->addError(
                new Error(
                    'An error occurred when creating the warehouse: ' . implode('.', $addResult->getErrorMessages()),
                    500,
                    $item
                )
            );
        }

        $primary = $this->getPrimaryField();
        $this->cache->set($item[$primary->getTo()], $addResult->getId());
        $result->setData($addResult->getId());

        // TODO: Event

        return $result;
    }

    public function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $primary = $this->getPrimaryField();
        $externalId = $item[$primary->getTo()] ?? '';

        $id = (int)$this->cache->get($externalId);
        $fields = $this->preparation($item);

        $beforeUpdate = $this->beforeUpdate($id, $fields);
        if (!$beforeUpdate->isSuccess()) {
            return $result->addErrors($beforeUpdate->getErrors());
        }

        if ($beforeUpdate->isStopped()) {
            return $result;
        }

        $updateResult = StoreTable::update($id, $fields['FIELDS']);
        if (!$updateResult->isSuccess()) {
            return $result->addError(
                new Error(
                    'An error occurred when updating warehouse: ' . implode('.', $updateResult->getErrorMessages()),
                    500,
                    $item
                )
            );
        }

        if (!$this->setUserFields($id, $fields['UF'])) {
            $result->addError(new Error('Error update user fields'));
        };

        $result->setData($id);

        // TODO: EVENT

        return $result;
    }

    public function isMultipleField(FieldInterface $field): bool
    {
        return false;
    }

    /**
     * Установка значений UF у склада
     *
     * @param int $id
     * @param array $fields
     * @return bool
     * @author Daniil S.
     */
    private function setUserFields(int $id, array $fields): bool
    {
        global $USER_FIELD_MANAGER;
        return $USER_FIELD_MANAGER->Update('CAT_STORE', $id, $fields);
    }

    /**
     * Преобразование данных склада
     *
     * @param array $fields
     * @return array{UF: array, FIELDS: array}
     * @since 1.1.1
     * @version 1.1.1
     */
    private function preparation(array $fields): array
    {
        $result = [
            'UF' => [],
            'FIELDS' => []
        ];

        if (array_key_exists('ACTIVE', $fields) && !isset($fields['ACTIVE'])) {
            unset($fields['ACTIVE']);
        }

        foreach ($fields as $code => $value) {
            $group = str_starts_with($code, 'UF_') ? 'UF' : 'FIELDS';
            $result[$group][$code] = $value;
        }

        return $result;
    }

    private function beforeAdd(array $fields): EventResult
    {
        // TODO: Logic
        $result = new EventResult;

        return $result;
    }

    private function beforeUpdate(int $id, array $fields): EventResult
    {
        // TODO: Logic
        $result = new EventResult;

        return $result;
    }

    /**
     * Проверка доступности модулей
     *
     * @return ResultInterface
     * @throws LoaderException
     * @since 1.1.1
     * @version 1.1.1
     */
    #[Validate]
    private function checkModules(): ResultInterface
    {
        $result = new Result;

        if (!Loader::includeModule('catalog')) {
            $result->addError(new Error('Module "catalog" not installed'));
        }

        return $result;
    }
}