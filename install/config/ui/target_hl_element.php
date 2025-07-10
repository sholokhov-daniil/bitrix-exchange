<?php

return [
    [
        'view' => 'entity-selector',
        'options' => [
            'title' => 'SHOLOKHOV_EXCHANGE_SETTINGS_UI_TITLE_RENDER_IBLOCK_SELECT_IBLOCK',
            'name' => 'fields[target][entity_id]',
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
];
