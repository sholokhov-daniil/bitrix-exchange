<?php

return [
    'TARGET_CODE' => 'target_hl_element',
    'MAP_CODE' => 'target_hl_element',
    'FIELDS' => json_encode(
        [
            [
                'view' => 'entity-selector',
                'name' => 'to',
                'title' => 'SHOLOKHOV_EXCHANGE_UI_ENTITY_PROPERTY_SELECTOR',
                'options' => [
                    'selector' => [
                        'multiple' => false,
                        'dialogOptions' => [
                            'entities' => [
                                [
                                    'id' => 'sholokhov-exchange-uf',
                                    'dynamicSearch' => true,
                                    'dynamicLoad' => true,
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'view' => 'input',
                'name' => 'from',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_MAP_UI_FROM',
                'options' => [],
            ],
            [
                'view' => 'checkbox',
                'name' => 'created_link',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_MAP_UI_CREATED_LINK',
                'options' => [
                    'attributes' => [
                        'checked' => true,
                    ]
                ],
            ]
        ]
    ),
];