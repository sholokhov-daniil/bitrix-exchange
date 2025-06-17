<?php

$path = $_SERVER["DOCUMENT_ROOT"] . '/%s/modules/sholokhov.exchange/admin/detail.php';

if (file_exists($file = sprintf($path, 'local'))) {
    @include $file;
} else {
    @include sprintf($path, 'bitrix');
}