<?php

namespace Sholokhov\BitrixExchange\Target\UserFields;

use Exception;
use CUserFieldEnum;

use Sholokhov\BitrixExchange\Exchange;
use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Helper\Helper;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;
use Sholokhov\BitrixExchange\Messages\Type\Error;
use Sholokhov\BitrixExchange\Repository\Fields\UFRepository;
use Sholokhov\BitrixExchange\Target\Attributes\BootstrapConfiguration;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class Enumeration extends Exchange
{
    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const BEFORE_UPDATE_EVENT = 'onBeforeUFEnumerationUpdate';

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const AFTER_UPDATE_EVENT = 'onAfterUFEnumerationUpdate';

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const BEFORE_ADD_EVENT = 'onBeforeUFEnumerationAdd';

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public const AFTER_ADD_EVENT = 'onAfterUFEnumerationAdd';

    /**
     * Получение идентификатора сущности которой относится пользовательское свойство(UF)
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getEntityId(): string
    {
        return $this->getOptions()->get('entity_id', '');
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
    protected function exists(array $item): bool
    {
        $primary = $this->getPrimaryField();

        if ($this->cache->has($item[$primary->getCode()])) {
            return true;
        }

        if ($enum = $this->searchEnum($item[$primary->getCode()])) {
            $this->cache->set($item[$primary->getCode()], $enum['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание значения списка
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function add(array $item): ResultInterface
    {
        global $APPLICATION;

        $result = new DataResult;
        $fields = $this->prepareItem($item);

        $beforeAdd = $this->beforeAdd($fields);
        if (!$beforeAdd->isSuccess()) {
            return $result->addErrors($beforeAdd->getErrors());
        }

        $uf = new CUserFieldEnum;
        $setResult = $uf->SetEnumValues(
            $this->getUserField()['ID'],
            [
                'n0' => $fields
            ]
        );

        if (!$setResult) {
            return $result->addError(
                new Error(
                    'An error occurred when creating the list value: ' . strip_tags($APPLICATION->GetException()),
                    500,
                    $fields
                )
            );
        }

        $primary = $this->getPrimaryField();
        $enum = $this->searchEnum($fields[$primary->getCode()]);
        $this->cache->set($item[$primary->getCode()], (int)$enum['ID']);
        $result->setData((int)$enum['ID']);

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['ID' => (int)$enum['ID'], 'FIELDS' => $fields, 'RESULT' => $result]))->send();

        return $result;
    }

    /**
     * Обновление значения списка
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function update(array $item): ResultInterface
    {
        $result = new DataResult;
        $primary = $this->getPrimaryField();

        $enumId = (int)$this->cache->get($item[$primary->getCode()]);

        if (!$enumId) {
            return $this->add($item);
        }

        $fields = $this->prepareItem($item);

        $beforeUpdate = $this->beforeUpdate($enumId, $fields);
        if (!$beforeUpdate->isSuccess()) {
            return $result->addErrors($beforeUpdate->getErrors());
        }

        $this->searchEnum($fields[$primary->getCode()]);

        $uf = new CUserFieldEnum;
        if (!$uf->SetEnumValues($this->getUserField()['ID'], [$enumId => $fields])) {
            return $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        $this->logger?->debug(sprintf('Updated the value of the list with the ID "%s"', $enumId));
        $result->setData($enumId);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['FIELDS' => $fields, 'ID' => $enumId, 'RESULT' => $result]))->send();

        return $result;
    }

    /**
     * Инициализация хранилища пользовательских свойств
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapUserFieldRepository(): void
    {
        $this->repository->set('uf_repository', new UFRepository([
            'entity_id' => $this->getEntityID()
        ]));
    }

    /**
     * Валидация конфигураций обмена
     *
     * @return ResultInterface
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[Validate]
    private function validateOptions(): ResultInterface
    {
        $result = new DataResult;

        if (!$this->getEntityId()) {
            $result->addError(new Error('entity_id is required'));
        }

        if (!$this->getPropertyCode()) {
            $result->addError(new Error('property_code is required'));
        }

        return $result;
    }

    /**
     * Поле в которое производится импорт является множественным
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function isMultipleField(FieldInterface $field): bool
    {
        return $this->getUserField()['MULTIPLE'] === 'Y';
    }

    /**
     * Найти значение списка
     *
     * @param mixed $value
     * @return array
     * @throws Exception
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function searchEnum(mixed $value): array
    {
        $primary = $this->getPrimaryField();
        $filter = [
            'USER_FIELD_ID' => $this->getUserField()['ID'],
            $primary->getCode() => $value,
        ];

        return CUserFieldEnum::GetList([], $filter)->Fetch() ?: [];
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
            if (in_array($field->getCode(), $supportedFields)) {
                $result[$field->getCode()] = $item[$field->getCode()];
            }
        }

        return $result;
    }

    /**
     * Получение хранилища импортированного поля
     *
     * @return array
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function getUserField(): array
    {
        return $this->getUfRepository()->get($this->getPropertyCode(), []);
    }

    /**
     * Получение хранилища информации о пользовательских свойствах (UF)
     *
     * @return UFRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function getUfRepository(): UFRepository
    {
        return $this->repository->get('uf_repository');
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
     * Список поддерживаемых полей значения свойства, для импортирования
     *
     * @return string[]
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private function getSupportedFields(): array
    {
        return ['VALUE', 'DEF', 'SORT', 'XML_ID'];
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
                $result->addError(new Error('Error adding UF list property: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
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
                $result->addError(new Error('Error updating UF list property: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }
}