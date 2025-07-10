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
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_ENCODE',
            'attributes' => [
                'name' => 'fields[source][encode]',
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_SEPARATOR',
            'attributes' => [
                'name' => 'fields[source][separator]',
                'maxlength' => 1,
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_ENCLOSURE',
            'attributes' => [
                'name' => 'fields[source][enclosure]',
            ]
        ]
    ],
    [
        'view' => 'input',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_ESCAPE',
            'attributes' => [
                'name' => 'fields[source][escape]',
            ]
        ]
    ],
];
