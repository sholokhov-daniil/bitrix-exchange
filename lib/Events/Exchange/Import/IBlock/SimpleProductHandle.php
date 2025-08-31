<?php

namespace Sholokhov\Exchange\Events\Exchange\Import\IBlock;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Sholokhov\Exchange\Target\IBlock\Catalog\SimpleProduct;
use Sholokhov\Exchange\Validators\CheckAvailableModules;

/**
 * Обработчики импорта простых товаров
 *
 * @internal
 */
class SimpleProductHandle
{
    /**
     * Получение доступных валидаторов
     *
     * @param Event $event
     * @return EventResult
     */
    public static function getValidators(Event $event): EventResult
    {
        $exchange = $event->getParameter('exchange');
        if (!($exchange instanceof SimpleProduct)) {
            return new EventResult(EventResult::UNDEFINED);
        }

        $validators = [
            new CheckAvailableModules(['catalog', 'currency']),
        ];

        return new EventResult(EventResult::SUCCESS, compact('validators'));
    }
}