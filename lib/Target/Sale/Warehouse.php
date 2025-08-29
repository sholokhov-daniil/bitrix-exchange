<?php

namespace Sholokhov\Exchange\Target\Sale;

use Exception;

use Sholokhov\Exchange\AbstractApplication;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Dispatcher\ExternalEventDispatcher;
use Sholokhov\Exchange\Dispatcher\ExternalEventTypes;
use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\ExchangeMapTrait;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Repository\Fields\UFRepository;
use Sholokhov\Exchange\Target\Attributes\Event;

use Bitrix\Catalog\StoreTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

/**
 * Производит импорт складов
 */
class Warehouse extends AbstractExchange
{
    use ExchangeMapTrait;

    protected UFRepository $ufRepository;

    /**
     * Получение ID значения из кеша
     *
     * @param array $item
     * @return int
     */
    protected function resolveId(array $item): int
    {
        $key = $this->getPrimaryField()->getTo();
        return (int)$this->cache->get($item[$key]);
    }

    /**
     * Логика проверки наличия элемента
     *
     * @param array $item
     * @return bool
     */
    protected function doExist(array $item): bool
    {
        $fieldKey = $this->getPrimaryField()->getTo();
        $externalId = $item[$fieldKey];

        $select = ['ID'];
        $filter = [$fieldKey=> $externalId];
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
     */
    public function add(array $item): DataResultInterface
    {
        $result = new DataResult;
        $fields = $this->preparation($item);

        $beforeAdd = $this->eventDispatcher?->beforeAdd($item);
        if (!$beforeAdd->isSuccess()) {
            return $result->addErrors($beforeAdd->getErrors());
        }

        if ($beforeAdd->isStopped()) {
            return $result;
        }

        $addResult = StoreTable::add($fields['FIELDS']);
        if (!$addResult->isSuccess()) {
            return $result->addError(
                new Error(
                    'An error occurred when creating the warehouse: ' . implode('.', $addResult->getErrorMessages()),
                    500
                )
            );
        }

        if (!$this->setUserFields($addResult->getId(), $fields['UF'])) {
            $result->addError(new Error('Error update user fields'));
        };

        $primary = $this->getPrimaryField();
        $this->cache->set($item[$primary->getTo()], $addResult->getId());
        $result->setData($addResult->getId());

        $this->eventDispatcher?->afterAdd($fields, $result);

        return $result;
    }

    public function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $primary = $this->getPrimaryField();
        $externalId = $item[$primary->getTo()] ?? '';

        $id = (int)$this->cache->get($externalId);
        $fields = $this->preparation($item);

        $beforeUpdate = $this->eventDispatcher?->beforeUpdate($id, $fields);
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
                    500
                )
            );
        }

        if (!$this->setUserFields($id, $fields['UF'])) {
            $result->addError(new Error('Error update user fields'));
        };

        $result->setData($id);

        $this->eventDispatcher?->afterUpdate($id, $fields, $result);

        return $result;
    }

    /**
     * Проверка множественности значения
     *
     * @param FieldInterface $field
     * @return bool
     */
    public function isMultipleField(FieldInterface $field): bool
    {
        $code = $field->getTo();

        if ($this->isUserField($code)) {
            $uf = $this->ufRepository->get($code);
            return $uf['MULTIPLE'] === 'Y';

        }

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
        return $USER_FIELD_MANAGER->Update($this->ufRepository->getId(), $id, $fields);
    }

    /**
     * Преобразование данных склада
     *
     * @param array $fields
     * @return array{UF: array, FIELDS: array}
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
            $group = $this->isUserField($code) ? 'UF' : 'FIELDS';
            $result[$group][$code] = $value;
        }

        return $result;
    }

    /**
     * Код свойства относится к пользовательским полням
     *
     * @param string $code
     * @return bool
     */
    private function isUserField(string $code): bool
    {
        return str_starts_with($code, 'UF_');
    }

    /**
     * Инициализация событий обмена
     *
     * @return void
     */
    #[Event(ExchangeEvent::BeforeRun)]
    private function beforeRun(): void
    {
        $this->ufRepository = new UFRepository(['entity_id' => 'CAT_STORE']);

        $types = new ExternalEventTypes;
        $types->beforeUpdate = 'onBeforeWarehouseUpdate';
        $types->afterUpdate = 'onAfterWarehouseUpdate';
        $types->beforeAdd = 'onBeforeWarehouseAdd';
        $types->afterAdd = 'onAfterWarehouseAdd';

        $this->eventDispatcher = new ExternalEventDispatcher($types, $this);

        if ($this->logger) {
            $this->eventDispatcher->setLogger($this->logger);
        }
    }

    protected function doAdd(array $fields): DataResultInterface
    {
        // TODO: Implement doAdd() method.
    }

    protected function doUpdate(int $id, array $fields): DataResultInterface
    {
        // TODO: Implement doUpdate() method.
    }

    protected function doExist(array $item): bool
    {
        // TODO: Implement doExist() method.
    }

    protected function prepareForAdd(array $item): array
    {
        // TODO: Implement prepareForAdd() method.
    }

    protected function prepareForUpdate(array $item): array
    {
        // TODO: Implement prepareForUpdate() method.
    }

    protected function resolveId(array $item): int
    {
        // TODO: Implement resolveId() method.
    }
}