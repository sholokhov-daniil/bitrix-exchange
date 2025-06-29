<?php

global $USER;

use Bitrix\Main\Localization\Loc;
use Sholokhov\Exchange\ORM\Settings\ExchangeTable;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/interface/admin_lib.php");


if (!$USER->IsAdmin()) {
    ShowMessage('Отказано в доступе');
    return;
}

$iterator = [];

$tableID = ExchangeTable::getTableName();
$sort = new CAdminSorting($tableID, "ID", "desc");
$list = new CAdminList($tableID, $sort);

$data = new CAdminResult(ExchangeTable::getList(), $tableID);
$data->NavStart(20);
$list->NavText($data->GetNavPrint('Элементы'));

$list->AddHeaders([
    [
        'id' => ExchangeTable::PC_HASH,
        'content' => 'ID',
        'sort' => ExchangeTable::PC_HASH,
        'default' => true,
    ],
    [
        'id' => ExchangeTable::PC_ACTIVE,
        'content' => Loc::getMessage('SHOLOKHOV_EXCHANGE_ADMIN_LIST_SETTINGS_COLUMN_ACTIVE'),
        'sort' => ExchangeTable::PC_ACTIVE,
        'default' => true,
    ],
    [
        'id' => ExchangeTable::PC_NAME,
        'content' => Loc::getMessage('SHOLOKHOV_EXCHANGE_ADMIN_LIST_SETTINGS_COLUMN_NAME'),
        'sort' => ExchangeTable::PC_NAME,
        'default' => true,
    ],
]);

while ($item = $data->Fetch()) {
    $row = $list->AddRow($item[ExchangeTable::PC_HASH], $item);

    $hash = "<a href='/bitrix/admin/sholokhov_exchange_detail.php?id={$item[ExchangeTable::PC_HASH]}'>{$item[ExchangeTable::PC_HASH]}</a>";
    $active = $item[ExchangeTable::PC_ACTIVE]
        ? Loc::getMessage('SHOLOKHOV_EXCHANGE_ADMIN_LIST_SETTINGS_APPEND')
        : Loc::getMessage('SHOLOKHOV_EXCHANGE_ADMIN_LIST_SETTINGS_REJECT');

    $row->AddViewField(ExchangeTable::PC_HASH, $hash);
    $row->AddViewField(ExchangeTable::PC_ACTIVE, $active);
    $row->AddViewField(ExchangeTable::PC_NAME, $item[ExchangeTable::PC_NAME]);
}

$list->AddAdminContextMenu([
    [
        "TEXT"	=> Loc::getMessage('SHOLOKHOV_EXCHANGE_ADMIN_LIST_SETTINGS_CREATE_BUTTON'),
        "LINK"	=> "/bitrix/admin/sholokhov_exchange_detail.php",
        "TITLE"	=> Loc::getMessage('SHOLOKHOV_EXCHANGE_ADMIN_LIST_SETTINGS_CREATE_BUTTON'),
        "ICON"	=> "btn_new"
    ]
]);
$list->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$list->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

