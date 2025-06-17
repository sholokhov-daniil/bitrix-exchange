<?php

namespace Sholokhov\Exchange\Http\Middleware;

use Sholokhov\Exchange\Helper\Helper;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Проверка прав доступа к модулю
 *
 * @since 1.2.0
 * @version 1.2.0
 */
final class ModuleRightMiddleware extends Base
{
    /**
     * @param Event $event
     * @return EventResult
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function onBeforeAction(Event $event): EventResult
    {
        global $APPLICATION;

        $moduleID = Helper::getModuleID();

        if ($APPLICATION->GetGroupRight($moduleID) <= 'D') {
            $this->addError(new Error(Loc::getMessage('SHOLOKHOV_EXCHANGE_MIDDLEWARE_ACCESS_DENIED'), 403));
            return new EventResult(EventResult::ERROR, null, $moduleID, $this);
        }

        return new EventResult(EventResult::SUCCESS, null, $moduleID, $this);
    }
}
