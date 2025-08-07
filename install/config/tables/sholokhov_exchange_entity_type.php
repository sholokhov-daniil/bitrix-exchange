<?php

use Bitrix\Main\ORM\Fields;

return [
    [
        'CODE' => (new Fields\StringField('CODE'))->configurePrimary(),
    ]
];