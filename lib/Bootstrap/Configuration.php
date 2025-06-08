<?php

namespace Sholokhov\Exchange\Bootstrap;

use DirectoryIterator;
use Sholokhov\Exchange\Helper\Helper;
use Bitrix\Main\Config\Configuration as Config;

/**
 * Производит инициализацию конфигураций модуля
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class Configuration
{
    /**
     * Выполнить автозагрузку
     *
     * @return void
     * @since 1.2.0
     * @version 1.2.0
     */
    public function bootstrap(): void
    {
        $registry = Config::getInstance(Helper::getModuleID());
        $iterator = new DirectoryIterator(Helper::getRootDir() . DIRECTORY_SEPARATOR . 'config');

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->isReadable()) {
                $registry->add($file->getBasename('.php'), @include $file->getPathname());
            }
        }
    }
}