<?php

namespace Sholokhov\Exchange\ORM;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\Result;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;

class DynamicEntitiesTable extends DataManager
{
    /**
     * ID процесса, которому принадлежит таблица
     */
    public const PC_PID = "PID";

    /**
     * Созданная таблица
     */
    public const PC_TABLE = "TABLE";

    public static function getTableName()
    {
        return 'sholokhov_exchange_dynamic_entity';
    }

    public static function getMap(): array
    {
        return [
            (new Fields\IntegerField('ID'))
                ->configureAutocomplete()
                ->configurePrimary(),
            (new Fields\IntegerField(self::PC_PID))
                ->configureRequired(),

            (new Fields\StringField(self::PC_TABLE))
                ->configureRequired()
                ->configureSize(255),
        ];
    }

    public static function addIfNotExist(string $table, ?int $pid = null): AddResult
    {
        $pid ??= getmypid();
        $filter = [
            self::PC_TABLE => $table,
            self::PC_PID => $pid,
        ];

        if ($row = self::getRow(compact('filter'))) {
            $result = new AddResult;
            $result->setId($row['ID']);
        } else {
            $result = self::add([
                self::PC_TABLE => $table,
                self::PC_PID => $pid,
            ]);
        }

        return $result;
    }
}