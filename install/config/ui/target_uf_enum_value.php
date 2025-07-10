<?php

return [
    [
        'view' => 'uf-property',
        'options' => [
            'iblock' => [
                'name' => 'fields[target][iblock_id]'
            ],
            'property' => [
                'name' => 'fields[target][property_id]',
                'api' => [
                    'propertyType' => 'enumeration'
                ]
            ]
        ]
    ]
];
