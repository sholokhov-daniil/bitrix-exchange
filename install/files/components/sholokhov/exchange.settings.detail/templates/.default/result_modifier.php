<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var CAdminTabControl $control */
$control = $arResult['CONTROL'];

$arResult['JS_DATA'] = [
    'OPTIONS' => [
        'signed' => $this->getComponent()->getSignedParameters(),
        'teleport' => [],
    ],
    'DATA' => [
        'id' => $arParams['ID']
    ]
];

foreach ($control->tabs as $tab) {
    $arResult['JS_DATA']['OPTIONS']['teleport'][$tab['CONTAINER']] = '#' . $tab['DIV'] . '_edit_table';
}