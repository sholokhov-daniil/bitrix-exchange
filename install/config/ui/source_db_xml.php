<?php

return [
    'ENTITY_CODE' => 'source_db_xml',
    'SETTINGS' => json_encode(
        [
            [
                'view' => 'input',
                'name' => 'path',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
                'options' => [],
            ],
            [
                'view' => 'input',
                'name' => 'rootTag',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_XML_ROOT',
                'options' => [],
            ]
        ]
    ),
];
