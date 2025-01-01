<?php

use Bitrix\Main\{Loader, LoaderException};

const NOT_CHECK_PERMISSIONS = true;
const NO_AGENT_CHECK = true;

$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__, 4);
$vendor = dirname(__DIR__, 2) . '/vendor/autoload.php';


if (file_exists($vendor)) {
    @require $vendor;
}

@require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!Loader::includeModule('sholokhov.exchange')) {
    throw new LoaderException('Module sholokhov.exchange not install');
}
