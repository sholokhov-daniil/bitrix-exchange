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

    private array $tables = [
        'sholokhov_exchange_result',
        'sholokhov_exchange_entity_type',
        'sholokhov_exchange_entities',
        'sholokhov_exchange_settings',
        'sholokhov_exchange_target_map',
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

    public function DoInstall(): void
    {
        $this->InstallDB();
        $this->InstallFiles();

        $this->registrationEvents();
        $this->Add();
        self::IncludeModule($this->MODULE_ID);
    }

    public function DoUninstall(): void
    {
        $this->UnInstallDB();
        
        $this->UnInstallFiles();
        $this->Remove();
    }

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

    public function UnInstallFiles(): void
    {
        $root = Loader::getDocumentRoot();

        DeleteDirFiles(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'admin',
            $root . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'admin'
        );

        Directory::deleteDirectory($root . DIRECTORY_SEPARATOR . 'bitrix' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'sholokhov');
    }

    public function InstallDB(): void
    {
        $this->dropTables();
        foreach ($this->tables as $table) {
            $data = @include $this->getConfigPath("tables/{$table}.php");

            $this->connection->createTable($table, ...$data);
        }

        $this->migration();
    }

    public function UnInstallDB(): void
    {
        $this->dropTables();
        $this->unRegistrationEvents();
    }

    private function migration(): void
    {
        $this->fillTable('sholokhov_exchange_entity_type', $this->getConfigPath('types'));
        $this->fillTable('sholokhov_exchange_entities', $this->getConfigPath('entities'));
        $this->fillTable('sholokhov_exchange_target_map', $this->getConfigPath('map'));
    }

    private function fillTable(string $table, string $path): void
    {
        $this->migrationFromConfig(
            $path,
            fn(array $config) => $this->connection->add($table, $config)
        );
    }

    
    private function migrationFromConfig(string $path, callable $callback): void
    {
        $directory = new Directory($path);
        $iterator = $directory->getChildren();

        foreach ($iterator as $children) {
            if ($children->isFile()) {
                $config = (array)@include $children->getPath();
                call_user_func($callback, $config);
            }
        }
    }

    private function dropTables(): void
    {
        for($i = count($this->tables) - 1; $i > 0; $i--) {
            $table = $this->tables[$i];

            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }
    }

    private function getConfigPath(string $name): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $name;
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
