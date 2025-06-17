<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

use Bitrix\Main\SystemException;
use Sholokhov\Exchange\Builder\DynamicEntity\TypeEntityEnum;
use Sholokhov\Exchange\ORM\Settings\EntitySettingsTable;
use Sholokhov\Exchange\ORM\Settings\EntityTypeTable;
use Sholokhov\Exchange\ORM\Settings\ExchangeTable;
use Sholokhov\Exchange\ORM\UI;
use Sholokhov\Exchange\Source;
use Sholokhov\Exchange\Target;
use Sholokhov\Exchange\ORM\Settings\EntityTable;
use Sholokhov\Exchange\ORM\ResultTable;

/**
 * @version 1.2.0
 */
class sholokhov_exchange extends CModule
{
    var $MODULE_ID = "sholokhov.exchange";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;

    private Connection $connection;

    public function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ .  DIRECTORY_SEPARATOR . "version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->connection = Application::getConnection();

        $this->MODULE_NAME = Loc::getMessage("SHOLOKHOV_EXCHANGE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("SHOLOKHOV_EXCHANGE_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "Шолохов Даниил";
        $this->PARTNER_URI = "https://github.com/sholokhov-daniil";
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     *
     * @version 1.2.0
     */
    public function DoInstall(): void
    {
        $this->registrationEvents();
        $this->Add();
        self::IncludeModule($this->MODULE_ID);

        $this->installDB();
    }

    /**
     * @return void
     * @throws SqlQueryException
     *
     * @version 1.2.0
     */
    public function DoUninstall(): void
    {
        self::IncludeModule($this->MODULE_ID);

        $dropTables = [
            ResultTable::getTableName(),
            ExchangeTable::getTableName(),
            UI\EntityUITable::getTableName(),
            EntitySettingsTable::getTableName(),
            EntityTable::getTableName(),
            EntityTypeTable::getTableName(),
        ];

        foreach ($dropTables as $table) {
            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }

        $this->unRegistrationEvents();

        $this->Remove();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function installDB(): void
    {
        $this->migrationEntities();
        $this->migrationUI();

        if ($this->connection->isTableExists(ResultTable::getTableName())) {
            $this->connection->dropTable(ResultTable::getTableName());
        }
        ResultTable::getEntity()->createDbTable();

        if ($this->connection->isTableExists(ExchangeTable::getTableName())) {
            $this->connection->dropTable(ExchangeTable::getTableName());
        }
        ExchangeTable::getEntity()->createDbTable();
    }

    private function migrationUI(): void
    {
        if ($this->connection->isTableExists(UI\EntityUITable::getTableName())) {
            $this->connection->dropTable(UI\EntityUITable::getTableName());
        }
        UI\EntityUITable::getEntity()->createDbTable();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     * @throws Exception
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function migrationEntities(): void
    {
        if ($this->connection->isTableExists(EntityTypeTable::getTableName())) {
            $this->connection->dropTable(EntityTypeTable::getTableName());
        }
        EntityTypeTable::getEntity()->createDbTable();

        if ($this->connection->isTableExists(EntityTable::getTableName())) {
            $this->connection->dropTable(EntityTable::getTableName());
        }
        EntityTable::getEntity()->createDbTable();

        if ($this->connection->isTableExists(EntitySettingsTable::getTableName())) {
            $this->connection->dropTable(EntitySettingsTable::getTableName());
        }
        EntitySettingsTable::getEntity()->createDbTable();

        $this->migrationSources();
        $this->migrationTargets();
    }

    /**
     * Миграция источников данных
     *
     * @return void
     * @throws Exception
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function migrationSources(): void
    {
        $typeResult = EntityTypeTable::add([
            EntityTypeTable::PC_CODE => TypeEntityEnum::Source->value
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'source_simple_xml',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Source\SimpleXml::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_NAME',
            EntityTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_DESC',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'source_db_xml',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Source\Xml::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_NAME',
            EntityTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_DESC',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'source_simple_csv',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Source\Csv::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_NAME',
            EntityTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_DESC',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'source_simple_json_file',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Source\JsonFile::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_NAME',
            EntityTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_DESC',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'source_iblock_element',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Source\Entities\IBlock\Element::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_ELEMENT_IBLOCK_NAME',
        ]);
    }

    /**
     * @return void
     * @throws Exception
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function migrationTargets(): void
    {
        $typeResult = EntityTypeTable::add([
            EntityTypeTable::PC_CODE => TypeEntityEnum::Target->value
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_file',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\File::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_FILE_NAME',
            EntityTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_TARGET_FILE_DESC',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_hl_element',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\Highloadblock\Element::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_HL_ELEMENT_NAME',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_iblock_element_simple_product',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\IBlock\Catalog\SimpleProduct::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_ELEMENT_SIMPLE_PRODUCT_NAME',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_iblock_property_enum_value',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\IBlock\Property\PropertyEnumeration::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_PROPERTY_ENUM_VALUE_NAME',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_iblock_element',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\IBlock\Element::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_ELEMENT_NAME',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_iblock_section',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\IBlock\Section::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_SECTION_NAME',
        ]);

        EntityTable::add([
            EntityTable::PC_CODE => 'target_uf_enum_value',
            EntityTable::PC_TYPE_CODE => $typeResult->getId(),
            EntityTable::PC_ENTITY => Target\UserFields\Enumeration::class,
            EntityTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_UF_ENUM_VALUE_NAME',
        ]);
    }

    private function registrationEvents(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible("main", "OnBeforeProlog", $this->MODULE_ID);
    }

    private function unRegistrationEvents(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler("main", "OnBeforeProlog", $this->MODULE_ID);
    }
}
