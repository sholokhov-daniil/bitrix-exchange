<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

final class sholokhov_exchange extends CModule
{
    public const PHP_MIN_VERSION = "8.3.0";
    var $MODULE_ID = "sholokhov.exchange";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME = "Шолохов Даниил";
        $this->PARTNER_URI = 'https://github.com/sholokhov-daniil';

        $this->MODULE_NAME = Loc::getMessage('SHOLOKHOV_EXCHANGE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('SHOLOKHOV_EXCHANGE_MODULE_DESCRIPTION');
    }

    public function DoInstall(): bool
    {
        global $APPLICATION;

        if (!$this->checkPhpVersion()) {
            $APPLICATION->ThrowException(Loc::getMessage('SHOLOKHOV_EXCHANGE_INVALID_PHP_VERSION', ['#VERSION#' => self::PHP_MIN_VERSION]));
            return false;
        }

        ModuleManager::registerModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall(): bool
    {
        ModuleManager::delete($this->MODULE_ID);
        return true;
    }

    private function checkPhpVersion(): bool
    {
        return version_compare(PHP_VERSION, self::PHP_MIN_VERSION, '>=');
    }
}