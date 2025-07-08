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

$tabs = [
    [
        'DIV' => 'general',
        'TAB' => "Основные",
        "TITLE" => 'Основные настройки',
    ],
    [
        'DIV' => 'target',
        'TAB' => "Обмен",
        "TITLE" => 'Настройки обмена данных',
    ],
    [
        'DIV' => 'source',
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
?>
<form method="POST">
    <?php
    $control->Begin();
    $control->BeginNextTab();
    ?>
    <div id="<?= $generalContainer ?>"></div>

    <?php
    $control->BeginNextTab();
    ?>
    <div id="<?= $targetContainer ?>"></div>

    <?php
    $control->BeginNextTab();
    ?>

    <div id="<?= $sourceController ?>"></div>
    <?php
    $control->BeginNextTab();
    ?>
    Тут настройки карты
    <?php
    $control->End();
    ?>
</form>

<?php
$options = [
    'container' => [
        'general' => '#' . $generalContainer,
        'target' => '#' . $targetContainer,
        'source' => '#' . $sourceController,
        'map' => ''
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
