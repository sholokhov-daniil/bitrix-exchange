<?php

return [
    'ENTITY_CODE' => 'target_uf_enum_value',
    'SETTINGS' => json_encode(
        [
            [
                'view' => 'uf-property',
                'name' => 'property',
                'options' => [
                    'iblock' => [],
                    'property' => [
                        'api' => [
                            'propertyType' => 'enumeration'
                        ]
                    ]
                ]
            ]
        ]
    )
];
