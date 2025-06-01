<?php

namespace Sholokhov\Exchange\ORM;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;

/**
 * Таблица с результатами обменов
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class ResultTable extends DataManager
{
    /**
     * Идентификатор записи
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public const PC_ID = "ID";

    /**
     * Идентификатор обмена
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public const PC_UID = 'UID';

    /**
     * ID процесса в котором был запущен обмен
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public const PC_PID = 'PID';

    /**
     * Результат обмена
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public const PC_VALUE = 'VALUE';

    /**
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getTableName(): string
    {
        return 'sholokhov_exchange_result';
    }

    /**
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getMap(): array
    {
        return [
            (new Fields\IntegerField(self::PC_ID))
                ->configureRequired()
                ->configurePrimary()
                ->configureAutocomplete(),

            (new Fields\StringField(self::PC_UID))
                ->configureRequired(),

            (new Fields\IntegerField(self::PC_PID))
                ->configureRequired(),

            new Fields\StringField(self::PC_VALUE),
        ];
    }

    /**
     * Получение всех данных по ID результата
     *
     * @param string $uid
     * @return Result
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getByUid(string $uid): Result
    {
        return self::query()
            ->where(self::PC_UID, $uid)
            ->exec();
    }
}