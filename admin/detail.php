<?php

global $APPLICATION;

use Bitrix\Main\Context;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$request = Context::getCurrent()->getRequest();

$APPLICATION->IncludeComponent(
        'sholokhov:exchange.settings.detail',
    '',
    [
        'ID' => $request->get('id'),
    ]
);
