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
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_ENCODE',
            'attributes' => [
                'name' => 'source[encode]',
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_SEPARATOR',
            'attributes' => [
                'name' => 'source[separator]',
                'maxlength' => 1,
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_ENCLOSURE',
            'attributes' => [
                'name' => 'source[enclosure]',
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_ESCAPE',
            'attributes' => [
                'name' => 'source[escape]',
            ]
        ]
    ],
];
