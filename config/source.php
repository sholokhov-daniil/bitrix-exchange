<?php

use Bitrix\Main\Localization\Loc;
use Sholokhov\Exchange\Source;

return [
    'simple_xml' => [
        'entity' => Source\SimpleXml::class,
        'name' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_NAME'),
        'description' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_DESC'),
    ],
    'db_xml' => [
        'entity' => Source\Xml::class,
        'name' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_NAME'),
        'description' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_DESC')
    ],
    'simple_csv' => [
        'entity' => Source\Csv::class,
        'name' => 'CSV',
        'description' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_DESC'),
    ],
    'simple_json_file' => [
        'entity' => Source\JsonFile::class,
        'name' => 'JSON',
        'description' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_DESC')
    ],
    'simple_json' => [
        'visible' => false,
        'entity' => Source\Json::class,
    ],
    'iblock_element' => [
        'entity' => Source\Entities\IBlock\Element::class,
        'name' => Loc::getMessage('SHOLOKHOV_EXCHANGE_SOURCE_ELEMENT_IBLOCK_NAME'),
    ],
];