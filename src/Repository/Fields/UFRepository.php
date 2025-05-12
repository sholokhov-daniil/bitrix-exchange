<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use CUserTypeEntity;
use InvalidArgumentException;

/**
 * Хранилище информации о пользовательских свойствах определенной сущности
 */
class UFRepository extends AbstractFieldRepository
{
    /**
     * Обновление информации определенного свойства
     *
     * @param string $code
     * @return void
     */
    public function refreshByCode(string $code): void
    {
        $field = $this->query(['FIELD_NAME' => $code]);
        $field ? $this->getStorage()->set($code, $field) : $this->getStorage()->delete($code);
    }

    /**
     * Проверка конфигурации
     *
     * @param array $options
     * @return void
     */
    protected function checkOptions(array $options): void
    {
        if (!is_string($options['ENTITY_ID']) || !mb_strlen($options['ENTITY_ID'])) {
            throw new InvalidArgumentException('Option "ENTITY_ID" should be a string');
        }
    }

    /**
     * Запрос на получение доступных свойств
     *
     * @final
     * @param array $parameters
     * @return array
     */
    final protected function query(array $parameters = []): array
    {
        $fields = [];
        $filter = ['ENTITY_ID' => $this->getEntityId()];

        if (is_array($parameters['filter'])) {
            $filter = array_merge($parameters['filter'], $filter);
        }

        $iterator = CUserTypeEntity::GetList([], $filter);

        while ($item = $iterator->Fetch()) {
            $fields[$item['FIELD_NAME']] = $item;
        }

        return $fields;
    }

    /**
     * Получение ID сущности, которому принадлежат поля
     *
     * @final
     * @return string
     */
    final protected function getEntityId(): string
    {
        return $this->getOptions()->get('ENTITY_ID');
    }

    /**
     * Получение идентификатора хранилища
     *
     * @return string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function getHash(): string
    {
        return static::class . '_' . $this->getEntityId();
    }
}