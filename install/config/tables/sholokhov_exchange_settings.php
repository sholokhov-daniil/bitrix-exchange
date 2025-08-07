<?php

use Bitrix\Main\ORM\Fields;

return [
    [
        'HASH' => new Fields\StringField('HASH'),
        'ACTIVE' => new Fields\BooleanField('ACTIVE'),
        'NAME' => new Fields\StringField('NAME'),
        'DESCRIPTION' => new Fields\StringField('DESCRIPTION'),
        'SETTINGS' => new Fields\TextField('SETTINGS'),
        'SOURCE_SETTINGS_ID' => new Fields\IntegerField('SOURCE_SETTINGS_ID'),
        'TARGET_SETTINGS_ID' => new Fields\IntegerField('TARGET_SETTINGS_ID'),
        'DATE_CREATE' => new Fields\DatetimeField('DATE_CREATE'),
        'DATE_UPDATE' => new Fields\DatetimeField('DATE_UPDATE'),
        'USER_ID_CREATED' => new Fields\IntegerField('USER_ID_CREATED'),
        'USER_ID_UPDATED' => new Fields\IntegerField('USER_ID_UPDATED'),
    ],
    ['HASH']
];