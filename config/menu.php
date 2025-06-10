<?php

use Sholokhov\Exchange\Helper\Helper;

return [
    [
        'menu_id' => 'sholokhov_exchange',
        'text' => 'Обмен',
        'title' => 'Какой-то обмен',
        'sort' => 500,
        'help_section' => Helper::getModuleID(),
        'items' => [
            [
                'text' => 'Пункт 1',
                'title' => 'Пункт 2',
                'url' => 'link',
                'sort' => 10
            ]
        ]
    ]
];