<?php

namespace Sholokhov\BitrixExchange\Prepares\File;

/**
 * Преобразование пользовательских свойств типа файл
 */
class UserField extends Description
{
    public function __construct(string $entityId)
    {
        $this->loadFields($entityId);
    }

    /**
     * Загрузка поддерживаемых свойств
     *
     * @param string $entityId
     * @return void
     */
    private function loadFields(string $entityId): void
    {
        $iterator = \CUserTypeEntity::GetList([], [
            'ENTITY_ID' => $entityId,
            'USER_TYPE_ID' => 'file'
        ]);

        while ($field = $iterator->Fetch()) {
            $this->addSupportedField($field['FIELD_NAME']);
        }
    }
}