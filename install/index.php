<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

use Bitrix\Main\SystemException;
use Sholokhov\Exchange\ORM\Admin\TargetTable;
use Sholokhov\Exchange\Source;
use Sholokhov\Exchange\Target;
use Sholokhov\Exchange\ORM\Admin\SourceTable;
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
    public function DoUninstall()
    {
        $this->unRegistrationEvents();

        $connection = Application::getConnection();
        $connection->dropTable(ResultTable::getTableName());
        $connection->dropTable(SourceTable::getTableName());
        $connection->dropTable(TargetTable::getTableName());

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
        $this->installSources();
        $this->installTarget();

        if (!Application::getConnection()->isTableExists(ResultTable::getTableName())) {
            ResultTable::getEntity()->createDbTable();
        }
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function installSources(): void
    {
        $table = SourceTable::getTableName();
        $connection = Application::getConnection();

        if ($connection->isTableExists($table)) {
            $connection->truncateTable($table);
        } else {
            SourceTable::getEntity()->createDbTable();
        }

        SourceTable::add([
            SourceTable::PC_CODE => 'simple_xml',
            SourceTable::PC_ENTITY => Source\SimpleXml::class,
            SourceTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_NAME',
            SourceTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_XML_DESC',
        ]);

        SourceTable::add([
            SourceTable::PC_CODE => 'db_xml',
            SourceTable::PC_ENTITY => Source\Xml::class,
            SourceTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_NAME',
            SourceTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_DB_XML_DESC',
        ]);

        SourceTable::add([
            SourceTable::PC_CODE => 'simple_csv',
            SourceTable::PC_ENTITY => Source\Csv::class,
            SourceTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_NAME',
            SourceTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_CSV_DESC',
        ]);

        SourceTable::add([
            SourceTable::PC_CODE => 'simple_json_file',
            SourceTable::PC_ENTITY => Source\JsonFile::class,
            SourceTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_NAME',
            SourceTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_SOURCE_SIMPLE_JSON_DESC',
        ]);

        SourceTable::add([
            SourceTable::PC_CODE => 'iblock_element',
            SourceTable::PC_ENTITY => Source\Entities\IBlock\Element::class,
            SourceTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_SOURCE_ELEMENT_IBLOCK_NAME',
        ]);
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private function installTarget(): void
    {
        $table = TargetTable::getTableName();
        $connection = Application::getConnection();

        if ($connection->isTableExists($table)) {
            $connection->truncateTable($table);
        } else {
            TargetTable::getEntity()->createDbTable();
        }

        TargetTable::add([
            TargetTable::PC_CODE => 'file',
            TargetTable::PC_ENTITY => Target\File::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_FILE_NAME',
            TargetTable::PC_DESCRIPTION => 'SHOLOKHOV_EXCHANGE_TARGET_FILE_DESC',
        ]);

        TargetTable::add([
            TargetTable::PC_CODE => 'hl_element',
            TargetTable::PC_ENTITY => Target\Highloadblock\Element::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_HL_ELEMENT_NAME',
        ]);

        TargetTable::add([
            TargetTable::PC_CODE => 'iblock_element_simple_product',
            TargetTable::PC_ENTITY => Target\IBlock\Catalog\SimpleProduct::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_ELEMENT_SIMPLE_PRODUCT_NAME',
        ]);

        TargetTable::add([
            TargetTable::PC_CODE => 'iblock_property_enum_value',
            TargetTable::PC_ENTITY => Target\IBlock\Property\PropertyEnumeration::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_PROPERTY_ENUM_VALUE_NAME',
        ]);

        TargetTable::add([
            TargetTable::PC_CODE => 'iblock_element',
            TargetTable::PC_ENTITY => Target\IBlock\Element::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_ELEMENT_NAME',
        ]);

        TargetTable::add([
            TargetTable::PC_CODE => 'iblock_section',
            TargetTable::PC_ENTITY => Target\IBlock\Section::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_IBLOCK_SECTION_NAME',
        ]);

        TargetTable::add([
            TargetTable::PC_CODE => 'uf_enum_value',
            TargetTable::PC_ENTITY => Target\UserFields\Enumeration::class,
            TargetTable::PC_NAME => 'SHOLOKHOV_EXCHANGE_TARGET_UF_ENUM_VALUE_NAME',
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
