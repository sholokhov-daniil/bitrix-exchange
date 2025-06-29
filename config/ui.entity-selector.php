<?php

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\UI\EntitySelector;

return [
    'entities' => [
        [
            'entityId' => EntitySelector\IBlock\IBlockProvider::ENTITY_ID,
            'provider' => [
                'moduleId' => Helper::getModuleID(),
                'className' => EntitySelector\IBlock\IBlockProvider::class
            ]
        ],
        [
            'entityId' => EntitySelector\IBlock\PropertyProvider::ENTITY_ID,
            'provider' => [
                'moduleId' => Helper::getModuleID(),
                'className' => EntitySelector\IBlock\PropertyProvider::class
            ]
        ],
        [
            'entityId' => EntitySelector\HighloadblockProvider::ENTITY_ID,
            'provider' => [
                'moduleId' => Helper::getModuleID(),
                'className' => EntitySelector\HighloadblockProvider::class
            ]
        ],
        [
            'entityId' => EntitySelector\UserFieldProvider::ENTITY_ID,
            'provider' => [
                'moduleId' => Helper::getModuleID(),
                'className' => EntitySelector\UserFieldProvider::class
            ]
        ],
    ],
];
