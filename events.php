<?php

use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Builder\ModuleMenu;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler(
    'main',
    'OnBuildGlobalMenu',
    (new ModuleMenu)->make(...)
);