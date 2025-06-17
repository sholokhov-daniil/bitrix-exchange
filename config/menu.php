<?php

use Sholokhov\Exchange\Helper\Helper;

return [
    [
        'menu_id' => 'sholokhov_exchange',
        'text' => 'Обмен данных',
        'sort' => 500,
        'help_section' => Helper::getModuleID(),
        'items' => [
            [
                'text' => 'Список обменов',
                'title' => 'Список обмена | Sholokhov exchange',
                'url' => '/bitrix/admin/sholokhov_exchange_list.php',
                'sort' => 10
            ]
        ]
    ]
];