<?php

use Bitrix\Main\IO\Directory;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

use Bitrix\Main\SystemException;

class sholokhov_exchange extends CModule
{
    var $MODULE_ID = "sholokhov.exchange";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;

    private Connection $connection;

    private array $dropTables = [
        'sholokhov_exchange_settings',
        'sholokhov_exchange_result',
        'sholokhov_exchange_entities',
        'sholokhov_exchange_entity_type',
        'sholokhov_exchange_entity_settings',
        'sholokhov_exchange_entity_ui',
    ];

    public function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ . DIRECTORY_SEPARATOR . "version.php");
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
     */
    public function DoInstall(): void
    {
        $this->InstallDB();
        $this->InstallFiles();

        $this->registrationEvents();
        $this->Add();
        self::IncludeModule($this->MODULE_ID);
    }

    /**
     * @return void
     * @throws SqlQueryException
     */
    public function DoUninstall(): void
    {
        foreach ($this->dropTables as $table) {
            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }

        $this->unRegistrationEvents();

        $this->UnInstallFiles();
        $this->Remove();
    }

    /**
     * @return void
     */
    public function InstallFiles(): void
    {
        $root = Loader::getDocumentRoot();

        CopyDirFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'admin',
            $root . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'admin',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'components',
            $root . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'components',
            true,
            true
        );
    }

    /**
     * @return void
     */
    public function UnInstallFiles(): void
    {
        $root = Loader::getDocumentRoot();

        DeleteDirFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'admin',
            $root . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'admin'
        );

        Directory::deleteDirectory($root . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'sholokhov');
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     */
    public function InstallDB(): void
    {
        foreach ($this->dropTables as $table) {
            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }

        $this->connection->createTable(
            'sholokhov_exchange_result',
            [
                'ID' => (new Fields\IntegerField('ID'))
            ],
            ['ID'],
            ['ID'],
        );

        $this->connection->createTable(
            'sholokhov_exchange_entity_type',
            [
                'CODE' => (new Fields\StringField('CODE'))->configurePrimary(),
            ]
        );

        $this->connection->createTable(
            'sholokhov_exchange_entities',
            [
                'CODE' => (new Fields\StringField('CODE')),
                'TYPE_CODE' => (new Fields\StringField('TYPE_CODE'))->configureRequired(),
                'ENTITY' => (new Fields\StringField('ENTITY'))->configureRequired(),
                'NAME' => (new Fields\StringField('NAME'))->configureRequired(),
                'DESCRIPTION' => (new Fields\StringField('DESCRIPTION'))->configureDefaultValue(''),
            ],
            ['CODE']
        );

        $this->connection->createTable(
            'sholokhov_exchange_settings',
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
        );

        $this->connection->createTable(
            'sholokhov_exchange_entity_ui',
            [
                'ID' => new Fields\IntegerField('ID'),
                'ENTITY_CODE' => new Fields\StringField('ENTITY_CODE'),
                'SETTINGS' => new Fields\TextField('SETTINGS'),
            ],
            ['ID'],
            ['ID']
        );


        $this->migrationEntities();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws SystemException
     * @throws Exception
     */
    private function migrationEntities(): void
    {
        $this->migrationEntityTypes();
        $this->migrationEntitySources();
        $this->migrationEntityUI();
    }

    private function migrationEntityTypes(): void
    {
        $this->connection->add('sholokhov_exchange_entity_type', ['CODE' => 'source']);
        $this->connection->add('sholokhov_exchange_entity_type', ['CODE' => 'target']);
        $this->connection->add('sholokhov_exchange_entity_type', ['CODE' => 'map']);
    }

    /**
     * Миграция источников данных
     *
     * @return void
     * @throws Exception
     */
    private function migrationEntitySources(): void
    {
        $directory = new Directory($this->getEntityConfigPath());
        $iterator = $directory->getChildren();

        foreach ($iterator as $children) {
            if ($children->isFile()) {
                $config = (array)@include $children->getPath();
                $this->connection->add('sholokhov_exchange_entities', $config);
            }
        }
    }

    private function migrationEntityUI(): void
    {
        $directory = new Directory($this->getEntityUiConfigPath());
        $iterator = $directory->getChildren();

        foreach ($iterator as $children) {
            if ($children->isFile()) {
                $config = (array)@include $children->getPath();

                $this->connection->add('sholokhov_exchange_entity_ui', $config);
            }
        }
    }

    private function getEntityConfigPath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'entities';
    }

    private function getEntityUiConfigPath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'ui';
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
