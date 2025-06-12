<?php

namespace Sholokhov\Exchange\ORM\Settings;

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\ORM\Data\DataManager;
use Sholokhov\Exchange\Helper\Json;

/**
 * Хранит настройки сущности
 *
 * @final
 * @since 1.2.0
 * @version 1.2.0
 */
final class EntitySettingsTable extends DataManager
{
    /**
     * Уникальный id настройки
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ID = "ID";

    /**
     * Символьный код объекта, которому принадлежат настройки
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ENTITY_CODE = "ENTITY_CODE";

    /**
     * Связь с объектом в таблице {@see EntityTable}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ENTITY = "ENTITY";

    /**
     * Настройки объекта
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_SETTINGS = "SETTINGS";

    /**
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getTableName(): string
    {
        return 'sholokhov_exchange_entity_settings';
    }

    /**
     * Описание структуры таблицы
     *
     * @return array
     * @throws SystemException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getMap(): array
    {
        return [
            (new Fields\IntegerField(self::PC_ID))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new Fields\StringField(self::PC_ENTITY_CODE))
                ->configureRequired(),

            (new Fields\StringField(self::PC_SETTINGS))
                ->addFetchDataModifier(Json::decode(...))
                ->addSaveDataModifier(Json::encode(...)),

            (new Fields\Relations\Reference(
                self::PC_ENTITY,
                EntityTable::class,
                Join::on('this.' . self::PC_ENTITY_CODE, 'ref.' . EntityTable::PC_CODE)
            ))
        ];
    }
}