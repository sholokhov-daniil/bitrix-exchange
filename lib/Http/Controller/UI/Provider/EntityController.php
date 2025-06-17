<?php

namespace Sholokhov\Exchange\Http\Controller\UI\Provider;

use Sholokhov\Exchange\ORM\UI\EntityUITable;
use Sholokhov\Exchange\Http\Middleware\ModuleRightMiddleware;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

/**
 * Контроллер работы с настройками визуальной части сущностей
 *
 * @since 1.2.0
 * @version 1.2.0
 */
final class EntityController extends Controller
{
    /**
     * Конфигурация обработчиков запроса
     *
     * @return array[]
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function configureActions(): array
    {
        return [
            'get' => [
                '+prefilters' => new ModuleRightMiddleware
            ]
        ];
    }

    /**
     * Получение настроек визуальной части
     *
     * @param string $code
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getAction(string $code): array
    {
        $row = EntityUITable::getRow([
            'filter' => [
                EntityUITable::PC_ENTITY_CODE => $code
            ],
            'cache' => ['ttl' => 36000]
        ]);

        return is_array($row[EntityUITable::PC_SETTINGS]) ? $row[EntityUITable::PC_SETTINGS] : [];
    }
}
