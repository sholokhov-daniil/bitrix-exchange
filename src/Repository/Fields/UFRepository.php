<?php

namespace Sholokhov\BitrixExchange\Repository\Fields;

use CUserTypeEntity;
use InvalidArgumentException;

/**
 * Хранилище информации о пользовательских свойствах определенной сущности
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @package Repository
 */
class UFRepository extends AbstractFieldRepository
{
    /**
     * Проверка конфигурации
     *
     * @param array $options
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
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
     *
     * @since 1.0.0
     * @version 1.0.0
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
     * Поиск свойства по символьному коду свойства
     *
     * @final
     * @param string $id
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function search(string $id): mixed
    {
        $iterator = $this->query([
            'filter' => ['FIELD_NAME' => $id]
        ]);

        return $iterator[$id] ?? null;
    }

    /**
     * Получение ID сущности, которому принадлежат поля
     *
     * @final
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
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