<?php

global $USER;

use Bitrix\Main\Context;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/interface/admin_lib.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


if (!$USER->IsAdmin()) {
    ShowMessage('Отказано в доступе');
    return;
}


$request = Context::getCurrent()->getRequest();

$data = [
    'id' => $request->get('id')
];
$containerId = uniqid('sholokhov_exchange_detail_');

\Bitrix\Main\UI\Extension::load('sholokhov.exchange.ui');
?>
<div id="<?= $containerId ?>"></div>
<script>
    BX.ready(function() {
        BX.loadExt('sholokhov.exchange.detail')
            .then(() => {
                window.Sholokhov.Exchange.Detail.mount('#<?= $containerId ?>', <?= json_encode($data) ?>);
            })
            .catch(response => console.error(response))
    });
</script>
Детальная страница настроек обмена
