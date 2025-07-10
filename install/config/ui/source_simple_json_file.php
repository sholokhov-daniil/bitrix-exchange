<?php

return [
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
            'attributes' => [
                'name' => 'fields[source][path]'
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_JSON_SOURCE_KEY',
            'attributes' => [
                'name' => 'fields[source][source_key]'
            ]
        ]
    ],
    [
        'view' => 'checkbox',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_FILE_PATH',
            'attributes' => [
                'name' => 'fields[source][multiple]'
            ]
        ]
    ],
];