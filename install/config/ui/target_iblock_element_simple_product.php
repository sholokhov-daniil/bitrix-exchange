<?php

return [
    'ENTITY_CODE' => 'target_iblock_element_simple_product',
    'SETTINGS' => json_encode(
        [
            [
                'view' => 'checkbox',
                'name' => 'deactivate',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_ENTITY_UI_TARGET_TITLE_FIELD_DEACTIVATE',
                'options' => []
            ],
            [
                'view' => 'entity-selector',
                'name' => 'iblock_id',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
                'options' => [
                    'selector' => [
                        'multiple' => false,
                        'addButtonCaption' => 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                        'dialogOptions' => [
                            'entities' => [
                                [
                                    'id' => 'sholokhov-exchange-iblock',
                                    'dynamicSearch' => true,
                                    'dynamicLoad' => true,
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    )
];
