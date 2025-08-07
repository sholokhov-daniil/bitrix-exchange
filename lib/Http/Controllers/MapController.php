<?php

namespace Sholokhov\Exchange\Http\Controllers;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Sholokhov\Exchange\Http\Middleware\ModuleRightMiddleware;
use Sholokhov\Exchange\ORM\Settings\EntityTable;
use Sholokhov\Exchange\ORM\UI\TargetMapTable;

/**
 * Контроллер карты обмена
 *
 * @since 1.2.0
 * @version 1.2.0
 */
final class MapController extends Controller
{
    /**
     * Конфигурация обработчиков контроллера
     *
     * @return array[]
     * @since 1.2.0
     * @version 1.2.0
     */
    public function configureActions(): array
    {
        return [
            'getTemplates' => [
                '+prefilters' => [new ModuleRightMiddleware]
            ]
        ];
    }

    /**
     * Получение доступных типов поля карты обмена
     *
     * @param string $target Цель обмена (куда импортируются данные)
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTemplatesAction(string $target): array
    {
        $result = [];

        try {
            $iterator = TargetMapTable::query()
                ->where(TargetMapTable::PC_TARGET_CODE, $target)
                ->addSelect(TargetMapTable::PC_ID)
                ->addSelect(TargetMapTable::PC_MAP_CODE)
                ->addSelect(TargetMapTable::PC_MAP . "." . EntityTable::PC_NAME, 'NAME')
                ->setCacheTtl(360000)
                ->exec();

            while ($item = $iterator->fetch()) {
                $result[] = [
                    'id' => (int)$item[TargetMapTable::PC_ID],
                    'code' => $item[TargetMapTable::PC_MAP_CODE],
                    'name' => $item['NAME'],
                ];
            }

        } catch (\Throwable $throwable) {
            $this->addError(new Error(Loc::getMessage('SHOLOKHOV_EXCHANGE_CONTROLLER_ENTITY_EXCEPTION'), 500));
        }

        return $result;
    }
}