<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {

    die('ss');
}

/** @var CAdminTabControl $control */
$control = $arResult['CONTROL'];
$control->Begin();
?>

<form method="POST">
    <?php
    foreach ($control->tabs as $tab) {
        $control->BeginNextTab();
    }

    if ($arResult['CUSTOM_TABS']) {
        $control->tabs = $arResult['CUSTOM_TABS'];

        foreach ($control->tabs as $tab) {
            $control->BeginNextTab();
        }
    }

    $control->Buttons([
        'btnSave' => true,
        'btnApply' => false,
        'btnSaveAndAdd' => false,
        'ajaxMode' => true,
    ]);
    $control->End();
    ?>
</form>

<script>
    BX.ready(function() {
        BX.loadExt('sholokhov.exchange.detail')
            .then(() => {
                const detail = new BX.Sholokhov.Exchange.Detail.Detail(
                    <?= json_encode($arResult['JS_DATA']['DATA']) ?>,
                    <?= json_encode($arResult['JS_DATA']['OPTIONS']) ?>
                );
                detail.view();
            })
            .catch(() => {

            })
    });
</script>