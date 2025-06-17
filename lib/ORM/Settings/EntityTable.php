<?php

namespace Sholokhov\Exchange\ORM\Settings;

use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;

/**
 * Хранит зарегистрированные источники данных обмена.
 *
 * Источники данных используются в административной части сайта
 *
 * @final
 * @since 1.2.0
 * @version 1.2.0
 */
final class EntityTable extends DataManager
{
    /**
     * Уникальный символьный код источника данных
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_CODE = "CODE";

    /**
     * Сущность позволяющая работать с источником данных
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_ENTITY = "ENTITY";

    /**
     * Наименование источника
     *
     * Наименование выводится в визуальной части.
     * В данном свойстве хранится код из языкового файла {@see Loc::getMessage()}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_NAME = "NAME";

    /**
     * Описание источника
     *
     * Описание выводится в визуальной части.
     * В данном свойстве хранится код из языкового файла {@see Loc::getMessage()}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_DESCRIPTION = "DESCRIPTION";

    /**
     * Тип сущности(группа)
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_TYPE_CODE = "TYPE_CODE";

    /**
     * Привязка к таблице типа сущности {@see EntityTypeTable}
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public const PC_TYPE = "TYPE";

    /**
     * Название таблицы
     *
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getTableName(): string
    {
        return 'sholokhov_exchange_entities';
    }

    /**
     * Описание столбцов с данными
     *
     * @return array
     *
     * @throws SystemException
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getMap(): array
    {
        return [
            (new Fields\StringField(self::PC_CODE))
                ->configurePrimary()
                ->configureRequired(),

            (new Fields\StringField(self::PC_TYPE_CODE))
                ->configureRequired(),

            (new Fields\StringField(self::PC_ENTITY))
                ->configureRequired(),

            (new Fields\StringField(self::PC_NAME))
                ->addFetchDataModifier(fn($value) => Loc::getMessage($value) ?: '')
                ->configureRequired(),

            (new Fields\StringField(self::PC_DESCRIPTION))
                ->addFetchDataModifier(fn($value) => Loc::getMessage($value) ?: '')
                ->configureDefaultValue(''),

            (new Fields\Relations\Reference(
                self::PC_TYPE,
                EntityTable::class,
                Join::on('this.' . self::PC_TYPE_CODE, 'ref.' . EntityTable::PC_CODE)
            )),
        ];
    }
}