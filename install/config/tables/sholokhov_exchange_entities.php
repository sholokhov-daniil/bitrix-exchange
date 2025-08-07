<?php

use Bitrix\Main\ORM\Fields;

return [
    [
        'CODE' => new Fields\StringField('CODE'),
        'TYPE_CODE' => (new Fields\StringField('TYPE_CODE'))->configureRequired(),
        'ENTITY' => (new Fields\StringField('ENTITY'))->configureRequired(),
        'NAME' => (new Fields\StringField('NAME'))->configureRequired(),
        'DESCRIPTION' => (new Fields\StringField('DESCRIPTION'))->configureDefaultValue(''),
    ],
    ['CODE']
];