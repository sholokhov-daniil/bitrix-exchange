<?php

namespace Sholokhov\Exchange\ORM\Settings;

use CUser;

use Sholokhov\Exchange\Helper\Json;

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

/**
 * Настройки обменов
 *
 * @since 1.2.0
 * @version 1.2.0
 */
final class ExchangeTable extends DataManager
{
    /**
     * Уникальный текстовый идентификатор обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_HASH = "HASH";

    /**
     * Активность обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ACTIVE = "ACTIVE";

    /**
     * Наименование обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_NAME = "NAME";

    /**
     * Описание обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_DESCRIPTION = "DESCRIPTION";

    /**
     * Общие настройки обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_SETTINGS = "SETTINGS";

    /**
     * Настройки источника данных
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_SOURCE_SETTINGS_ID = "SOURCE_SETTINGS_ID";

    /**
     * Связь с записью в таблице хранения настроек источника данных
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_SOURCE_SETTINGS = "SOURCE_SETTINGS";

    /**
     * Настройки способа обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_TARGET_SETTINGS_ID = "TARGET_SETTINGS_ID";

    /**
     * Связь с записью в таблице хранения настроек способа обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_TARGET_SETTINGS = "TARGET_SETTINGS";

    /**
     * Дата создания обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_DATE_CREATE = "DATE_CREATE";

    /**
     * Дата обновления настроек обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_DATE_UPDATE = "DATE_UPDATE";

    /**
     * Пользователь создавший настройки обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_USER_ID_CREATED = "USER_ID_CREATED";

    /**
     * Пользователь обновивший настройки обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_USER_ID_UPDATED = "USER_ID_UPDATED";

    /**
     * Связь с таблицей, где хранится пользователь создавший настройки обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_USER_CREATED = "USER_CREATED";

    /**
     * Связь с таблицей, где хранится пользователь обновивший настройки обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_USER_UPDATED = "USER_UPDATED";

    /**
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getTableName(): string
    {
        return 'sholokhov_exchange_settings';
    }

    /**
     * @return array
     *
     * @throws SystemException
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getMap(): array
    {
        return [
            (new Fields\StringField(self::PC_HASH))
                ->configurePrimary(),

            (new Fields\BooleanField(self::PC_ACTIVE))
                ->configureRequired()
                ->configureDefaultValue(true),

            (new Fields\StringField(self::PC_NAME))
                ->configureSize(255)
                ->configureDefaultValue(''),

            (new Fields\StringField(self::PC_DESCRIPTION))
                ->configureDefaultValue(''),

            (new Fields\TextField(self::PC_SETTINGS))
                ->configureDefaultValue('')
                ->addFetchDataModifier(Json::decode(...))
                ->addSaveDataModifier(Json::encode(...)),

            (new Fields\IntegerField(self::PC_SOURCE_SETTINGS_ID))
                ->configureRequired(),

            (new Fields\IntegerField(self::PC_TARGET_SETTINGS_ID))
                ->configureRequired(),

            (new Fields\DatetimeField(self::PC_DATE_CREATE))
                ->configureRequired()
                ->configureDefaultValue(new DateTime),

            (new Fields\DatetimeField(self::PC_DATE_UPDATE))
                ->configureRequired()
                ->configureDefaultValue(new DateTime)
                ->addSaveDataModifier(fn() => new DateTime),

            (new Fields\IntegerField(self::PC_USER_ID_CREATED))
                ->configureRequired()
                ->configureDefaultValue((new CUser)->GetID()),

            (new Fields\IntegerField(self::PC_USER_ID_UPDATED))
                ->configureRequired()
                ->addSaveDataModifier(fn() => (int)(new CUser)->GetID()),

            (new Fields\Relations\Reference(
                self::PC_SOURCE_SETTINGS,
                EntitySettingsTable::class,
                Join::on('this.' . self::PC_SOURCE_SETTINGS_ID, 'ref.ID')
            )),

            (new Fields\Relations\Reference(
                self::PC_TARGET_SETTINGS,
                EntitySettingsTable::class,
                Join::on('this.' . self::PC_TARGET_SETTINGS, 'ref.ID')
            )),

            (new Fields\Relations\Reference(
                self::PC_USER_CREATED,
                UserTable::class,
                Join::on('this.' . self::PC_USER_ID_CREATED, 'ref.ID')
            )),

            (new Fields\Relations\Reference(
                self::PC_USER_UPDATED,
                UserTable::class,
                Join::on('this.' . self::PC_USER_UPDATED, 'ref.ID')
            )),
        ];
    }
}