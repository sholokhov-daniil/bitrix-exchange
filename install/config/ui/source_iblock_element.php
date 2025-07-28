<?php

return [
    'ENTITY_CODE' => 'source_iblock_element',
    'SETTINGS' => json_encode(
        [
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