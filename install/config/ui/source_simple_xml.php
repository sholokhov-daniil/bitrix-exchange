<?php

return [
    'ENTITY_CODE' => 'source_simple_xml',
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
                'name' => 'encode',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_ENCODE',
                'options' => []
            ],
            [
                'view' => 'input',
                'name' => 'separator',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_SEPARATOR',
                'options' => [
                    'attributes' => [
                        'maxlength' => 1,
                    ]
                ]
            ],
            [
                'view' => 'input',
                'name' => 'enclosure',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_ENCLOSURE',
                'options' => []
            ],
            [
                'view' => 'input',
                'name' => 'escape',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_SOURCE_CSV_ESCAPE',
                'options' => []
            ],
        ]
    )
];
