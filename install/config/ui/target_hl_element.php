<?php

return [
    'ENTITY_CODE' => 'target_hl_element',
    'SETTINGS' => json_encode(
        [
            [
                'view' => 'entity-selector',
                'name' => 'entity_id',
                'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_HL_SELECT_IBLOCK',
                'options' => [
                    'selector' => [
                        'multiple' => false,
                        'addButtonCaption' => 'SHOLOHKOV_EXCHANGE_UI_ENTITY_SELECTOR_DIALOG_ADD_BUTTON_CAPTION_SELECT',
                        'dialogOptions' => [
                            'entities' => [
                                [
                                    'id' => 'sholokhov-exchange-highloadblock',
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
