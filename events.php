<?php

use Sholokhov\Exchange\Events\Exchange\WarehouseHandlers;
use Sholokhov\Exchange\Helper\Helper;

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler(
    Helper::getModuleID(),
    'onBeforeCreateValidator',
    WarehouseHandlers::getValidators(...)
);