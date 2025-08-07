<?php

use Bitrix\Main\ORM\Fields;

return [
    [
        'ID' => new Fields\IntegerField('ID'),
        'UID' => new Fields\StringField('UID'),
        'PID' => new Fields\IntegerField('PID'),
        'VALUE' => new Fields\StringField('VALUE'),
    ],
    ['ID'],
    ['ID'],
];