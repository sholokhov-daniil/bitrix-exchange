<?php

return [
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
            'attributes' => [
                'name' => 'source[path]'
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_JSON_SOURCE_KEY',
            'attributes' => [
                'name' => 'source[source_key]'
            ]
        ]
    ],
    [
        'view' => 'checkbox',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
            'attributes' => [
                'name' => 'source[multiple]'
            ]
        ]
    ],
];