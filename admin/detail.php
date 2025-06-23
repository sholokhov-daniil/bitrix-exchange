<?php

global $USER;

use Bitrix\Main\Context;
use Bitrix\Main\Event;
use Sholokhov\Exchange\Helper\Helper;

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
$targetContainer = uniqid('sholokhov_exchange_detail_target_');

(new Event(Helper::getModuleID(), 'beforeRenderDetailSettings'))->send();
?>
Тут как-то нужно инициализироваться

<div id="<?= $targetContainer ?>"></div>
<script>
    BX.ready(function() {
         BX.loadExt('sholokhov.exchange.detail')
             .then(() => {

                 const target = new BX.Sholokhov.Exchange.Detail.TargetSettings(
                     '#<?= $targetContainer ?>',
                     <?= json_encode($data) ?>
                 );
                 target.view();

                //BX.Sholokhov.Exchange.Detail.mount('#<?php //= $containerId ?>//', <?php //= json_encode($data) ?>//);
             })
             .catch(response => console.error(response))
    });
</script>
Детальная страница настроек обмена
