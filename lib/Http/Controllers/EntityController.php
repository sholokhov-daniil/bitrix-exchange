<?php

namespace Sholokhov\Exchange\Http\Controllers;

use Throwable;

use Sholokhov\Exchange\Http\Middleware\ModuleRightMiddleware;
use Sholokhov\Exchange\ORM\Settings\EntityTable;
use Sholokhov\Exchange\ORM\UI\EntityUITable;

use Bitrix\Main\Error;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Localization\Loc;

/**
 * @internal
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
            'getByType' => [
                '+prefilters' => [
                    new ModuleRightMiddleware
                ],
            ],
            'getFields' => [
                '+prefilters' => [
                    new ModuleRightMiddleware
                ],
            ],
        ];
    }

    /**
     * Получение сущностей по типу
     *
     * @param string $code
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getByTypeAction(string $code): array
    {
        $result = [];

        try {
            $result = EntityTable::getList([
                'filter' => [
                    EntityTable::PC_TYPE_CODE => $code
                ],
                'cache' => ['ttl' => 36000]
            ])->fetchAll();
        } catch (Throwable) {
            $this->addError(new Error(Loc::getMessage('SHOLOKHOV_EXCHANGE_CONTROLLER_ENTITY_EXCEPTION'), 500));
        }

        return $result;
    }

    /**
     * Получение доступных полей сущности
     *
     * @param string $code
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getFieldsAction(string $code): array
    {
        $result = [];

        try {
            $result = EntityUITable::getList([
                'filter' => [
                    EntityUITable::PC_ENTITY_CODE => $code
                ],
                'cache' => ['ttl' => 36000]
            ])->fetchAll();
        } catch (Throwable) {
            $this->addError(new Error(Loc::getMessage('SHOLOKHOV_EXCHANGE_CONTROLLER_ENTITY_EXCEPTION'), 500));
        }

        return $result;
    }
}