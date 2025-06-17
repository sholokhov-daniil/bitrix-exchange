<?php

namespace Sholokhov\Exchange\ORM\UI;

use Sholokhov\Exchange\ORM\Settings\EntityTable;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;

/**
 * Хранилище связей сущностей и свойств описывающих карту
 *
 * @final
 * @since 1.2.0
 * @version 1.2.0
 */
final class TargetMapTable extends DataManager
{
    /**
     * Идентификатор записи
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ID = "ID";

    /**
     * Принадлежность к обмену
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_TARGET_CODE = "TARGET_CODE";

    /**
     * Связь с записью сущности обмена
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_TARGET = "TARGET";

    /**
     * Принадлежность к свойству карты
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_MAP_CODE = "MAP_CODE";

    /**
     * Связь с записью сущности карты
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_MAP = "MAP";

    /**
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getTableName(): string
    {
        return 'sholokhov_exchange_target_map';
    }

    /**
     * Карта свойств сущности
     *
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

            (new Fields\StringField(self::PC_TARGET_CODE))
                ->configureRequired(),

            (new Fields\StringField(self::PC_MAP_CODE))
                ->configureRequired(),

            (new Fields\Relations\Reference(
                self::PC_TARGET,
                EntityTable::class,
                Join::on('this.' . self::PC_TARGET_CODE, 'ref.' . EntityTable::PC_CODE)
            )),

            (new Fields\Relations\Reference(
                self::PC_MAP,
                EntityTable::class,
                Join::on('this.' . self::PC_MAP_CODE, 'ref.' . EntityTable::PC_CODE)
            )),
        ];
    }
}
