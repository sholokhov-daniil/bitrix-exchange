<?php

namespace Sholokhov\Exchange\ORM\UI;

use Sholokhov\Exchange\Helper\Json;
use Sholokhov\Exchange\ORM\Settings\EntityTable;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;

/**
 * @final
 * @since 1.2.0
 * @version 1.2.0
 */
final class EntityUITable extends DataManager
{
    /**
     * ID записи
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ID = "ID";

    /**
     * Код сущности, которой относится UI
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ENTITY_CODE = "ENTITY_CODE";

    /**
     * Сущность, которой относится UI
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ENTITY = "ENTITY";

    /**
     * Механизм отрисовки
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
        return 'sholokhov_exchange_entity_ui';
    }

    /**
     * @return array
     * @throws SystemException
     * @throws ArgumentException
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
                ->configureRequired()
                ->configureUnique(),

            (new Fields\StringField(self::PC_SETTINGS))
                ->addFetchDataModifier(Json::decode(...))
                ->addSaveDataModifier(Json::encode(...))
                ->configureRequired(),

            (new Fields\Relations\Reference(
                self::PC_ENTITY,
                EntityTable::class,
                Join::on('this.' . self::PC_ENTITY_CODE, 'ref.' . EntityTable::PC_CODE)
            ))
        ];
    }
}