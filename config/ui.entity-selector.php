<?php

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\UI\EntitySelector;

return [
    'entities' => [
        [
            'entityId' => EntitySelector\IBlockProvider::ENTITY_ID,
            'provider' => [
                'moduleId' => Helper::getModuleID(),
                'className' => EntitySelector\IBlockProvider::class
            ]
        ]
    ],
];
