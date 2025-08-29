<?php

namespace Sholokhov\Exchange\Target\UserFields;

use Exception;
use CUserFieldEnum;

use Sholokhov\Exchange\Exception\Target\ExchangeItemStoppedException;
use Sholokhov\Exchange\AbstractApplication;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\EventResult;
use Sholokhov\Exchange\Messages\Type\ExchangeResult;
use Sholokhov\Exchange\Messages\Type\Result;
use Sholokhov\Exchange\Repository\Fields\UFRepository;
use Sholokhov\Exchange\Target\Attributes\Validate;
use Sholokhov\Exchange\Target\Attributes\Configuration;

use Bitrix\Main\Event;
use Sholokhov\Exchange\Messages\Type\Error;
use Bitrix\Main\EventResult as BXEventResult;

class Enumeration extends AbstractApplication
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
        $primary = $this->getPrimaryField();

        if ($this->cache->has($item[$primary->getTo()])) {
            return true;
        }

        if ($enum = $this->searchEnum($item[$primary->getTo()])) {
            $this->cache->set($item[$primary->getTo()], $enum['ID']);
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
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function add(array $item): DataResultInterface
    {
        global $APPLICATION;

        $result = new DataResult;
        $fields = $this->prepareItem($item);

        $beforeAdd = $this->beforeAdd($fields);
        if (!$beforeAdd->isSuccess()) {
            return $result->addErrors($beforeAdd->getErrors());
        }

        if ($beforeAdd->isStopped()) {
            return $result;
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
        $enum = $this->searchEnum($fields[$primary->getTo()]);
        $this->cache->set($item[$primary->getTo()], (int)$enum['ID']);
        $result->setData((int)$enum['ID']);

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['id' => (int)$enum['ID'], 'fields' => $fields, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Обновление значения списка
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $primary = $this->getPrimaryField();

        $enumId = (int)$this->cache->get($item[$primary->getTo()]);

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

        $this->searchEnum($fields[$primary->getTo()]);

        $uf = new CUserFieldEnum;
        if (!$uf->SetEnumValues($this->getUserField()['ID'], [$enumId => $fields])) {
            return $result->addError(new Error('An error occurred when creating the list value', 500, $fields));
        }

        $this->logger?->debug(sprintf('Updated the value of the list with the ID "%s"', $enumId));
        $result->setData($enumId);

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['fields' => $fields, 'id' => $enumId, 'result' => $result]))->send();

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
    #[Configuration]
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
        $result = new Result;

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
    public function isMultipleField(FieldInterface $field): bool
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
            $primary->getTo() => $value,
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
            if (in_array($field->getTo(), $supportedFields)) {
                $result[$field->getTo()] = $item[$field->getTo()];
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
                    $result->addError(new Error('Error adding UF list property: stopped', 300, $item));
                } else {
                    foreach ($parameters['ERRORS'] as $error) {
                        $result->addError(new Error($error, 300, $item));
                    }
                }
            }
        } catch (ExchangeItemStoppedException $exception) {
            $stoppedMessage = $exception->getMessage() ?: 'Adding of the UF property has been stopped:' . json_encode($item);
            $this->logger?->warning($stoppedMessage);

            $result->setStopped();
        }

        return $result;
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
                if (empty($parameters['errors']) || !is_array($parameters['errors'])) {
                    $result->addError(new Error('Error updating UF list property: stopped', 300, $item));
                } else {
                    foreach ($parameters['errors'] as $error) {
                        $result->addError(new Error($error, 300, $item));
                    }
                }
            }
        } catch (ExchangeItemStoppedException $exception) {
            $stoppedMessage = $exception->getMessage() ?: ('Updating of the UF property has been stopped: ' . json_encode($item));
            $this->logger?->warning($stoppedMessage);
            $result->setStopped();
        }

        return $result;
    }
}