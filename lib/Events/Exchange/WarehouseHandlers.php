<?php

namespace Sholokhov\Exchange\Events\Exchange;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Sholokhov\Exchange\Target\Sale\Warehouse;
use Sholokhov\Exchange\Validators\CheckAvailableModules;

class WarehouseHandlers
{
    /**
     * Получение валидаторов импорта складов
     *
     * @param Event $event
     * @return EventResult
     */
    public static function getValidators(Event $event): EventResult
    {
        $exchange = $event->getParameter('exchange');
        if (!($exchange instanceof Warehouse)) {
            return new EventResult(EventResult::UNDEFINED);
        }

        $validators = [
            new CheckAvailableModules(['catalog'])
        ];

        return new EventResult(EventResult::SUCCESS, compact('validators'));
    }
}