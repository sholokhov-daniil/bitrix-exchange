<?php

namespace Sholokhov\Exchange\Builder;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\SystemException;

/**
 * Сборщик ORM сущности описывающую таблицу хранения значений пользовательских свойств (UF) раздела.
 *
 * @since 1.1.0
 * @version 1.1.0
 */
class SectionUtsBuilder
{
    /**
     * @param int $iBlockId ID раздела, для которого генерируется сущность
     * @since 1.1.0
     * @version 1.1.0
     */
    public function __construct(private int $iBlockId)
    {
    }

    /**
     * Создание ORM сущности
     *
     * @param Field[] $fields
     * @return Entity
     * @throws ArgumentException
     * @throws SystemException
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function make(array $fields): Entity
    {
        $entityName = $this->getEntityName();
        $fields[] = new IntegerField('VALUE_ID');

        if (Entity::has($entityName)) {
            $entity = Entity::get($entityName);

            foreach ($fields as $field) {
                if (!$entity->hasField($field)) {
                    $entity->addField($field, $field->getName());
                }
            }
        } else {
            $entity = Entity::compileEntity($entityName, $fields, ['table_name' => $this->getTableName()]);
        }

        return $entity;
    }

    /**
     * Получение наименования сущности.
     *
     * @return string
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function getEntityName(): string
    {
        return 'sholokhov_exchange_' . $this->getTableName();
    }

    /**
     * Получение наименования таблицы на основе которой создается сущность
     *
     * @return string
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function getTableName(): string
    {
        return "b_uts_iblock_{$this->getIBlockId()}_section";
    }

    /**
     * Указание ID раздела, для которого необходима генерация ORM uts
     *
     * @param int $iBlockId
     * @return $this
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function setIBlockId(int $iBlockId): self
    {
        $this->iBlockId = $iBlockId;
        return $this;
    }

    /**
     * ID раздела на основе которого производится генерация сущности
     *
     * @return int
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function getIBlockId(): int
    {
        return $this->iBlockId;
    }
}