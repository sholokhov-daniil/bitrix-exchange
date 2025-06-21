<?php

namespace Sholokhov\Exchange\Http\Middleware;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Производит проверку доступности модуля и подгружает его
 *
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
final class ModuleLoaderMiddleware extends Base
{
    /**
     * @param string $module Модуль, который необходимо проверить и подгрузить
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function __construct(private readonly string $module)
    {
        parent::__construct();
    }

    /**
     * @param Event $event
     * @return EventResult
     * @throws LoaderException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function onBeforeAction(Event $event): EventResult
    {
        if (!Loader::includeModule($this->module)) {
            $this->addError(
                new Error(
                    Loc::getMessage(
                        'SHOLOKHOV_EXCHANGE_MIDDLEWARE_MODULE_NOT_FOUND',
                        ['#ID#' => $this->module]
                    ),
                    404
                )
            );
            return new EventResult(EventResult::ERROR, null, $this->module, null);
        }

        return new EventResult(EventResult::SUCCESS, null, $this->module, null);
    }
}