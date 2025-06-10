<?php

global $USER;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/interface/admin_lib.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


if (!$USER->IsAdmin()) {
    ShowMessage('Отказано в доступе');
    return;
}

$iterator = [];

$result = new CAdminList('table');
$result->NavStart();
