<?php

global $USER;

use Bitrix\Main\Context;
use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use Sholokhov\Exchange\Helper\Helper;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/interface/admin_lib.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


if (!$USER->IsAdmin()) {
    ShowMessage('Отказано в доступе');
    return;
}

$request = Context::getCurrent()->getRequest();

$data = [
    'id' => $request->get('id')
];

$generalContainer = uniqid('sholokhov_exchange_detail_general_');
$targetContainer = uniqid('sholokhov_exchange_detail_target_');
$sourceController = uniqid('sholokhov_exchange_detail_source_');
$mapController = uniqid('sholokhov_exchange_detail_map_');

$tabs = [
    [
        'DIV' => $generalContainer,
        'TAB' => "Основные",
        "TITLE" => 'Основные настройки',
    ],
    [
        'DIV' => $targetContainer,
        'TAB' => "Обмен",
        "TITLE" => 'Настройки обмена данных',
    ],
    [
        'DIV' => $sourceController,
        'TAB' => "Источник данных",
        "TITLE" => 'Настройки источника данных',
    ],
    [
        'DIV' => 'map',
        'TAB' => "Карта обмена",
        "TITLE" => 'Настройки карты обмена',
    ],
];
$control = new CAdminTabControl('se_detail_control', $tabs);

(new Event(Helper::getModuleID(), 'beforeRenderDetailSettings', $data))->send();

$control->Begin();
?>
<form method="POST">
    <?php
    array_walk($tabs, fn() => $control->BeginNextTab());
    $control->Buttons([
        'btnSave' => true,
        'btnApply' => false,
        'btnSaveAndAdd' => false,
        'ajaxMode' => true,
    ]);
    $control->End();
    ?>
</form>

<?php
$options = [
    'container' => [
        'general' => "#{$generalContainer}_edit_table",
        'target' => "#{$targetContainer}_edit_table",
        'source' => "#{$sourceController}_edit_table",
        'map' => "#{$mapController}_edit_table",
    ]
];
?>
<script>
    BX.ready(function() {
        BX.loadExt('sholokhov.exchange.detail')
            .then(() => {
                const detail = new BX.Sholokhov.Exchange.Detail.Detail(<?= json_encode($data) ?>, <?= json_encode($options) ?>);
                detail.view();
            })
            .catch(() => {

            })
    });
</script>
