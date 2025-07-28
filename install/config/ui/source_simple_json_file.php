<?php

return [
    'ENTITY_CODE' => 'source_simple_json_file',
    'SETTINGS' => json_encode(
        [
            [
                'view' => 'input',
                'name' => 'path',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
                'options' => []
            ],
            [
                'view' => 'input',
                'name' => 'source_key',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_JSON_SOURCE_KEY',
                'options' => []
            ],
            [
                'view' => 'checkbox',
                'name' => 'multiple',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
                'options' => []
            ],
        ]
    )
];