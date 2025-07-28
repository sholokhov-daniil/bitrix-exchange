<?php

return [
    'ENTITY_CODE' => 'target_iblock_property_enum_value',
    'SETTINGS' => json_encode(
        [
            [
                'view' => 'iblock-property',
                'name' => 'property',
                'isConstructor' => true,
                'options' => [
                    'iblock' => [],
                    'property' => [
                        'api' => [
                            'propertyType' => 'L'
                        ]
                    ]
                ]
            ]
        ]
    )
];