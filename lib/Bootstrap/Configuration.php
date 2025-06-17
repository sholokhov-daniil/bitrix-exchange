<?php

namespace Sholokhov\Exchange\Bootstrap;

use DirectoryIterator;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Helper\Config;

/**
 * Производит инициализацию конфигураций модуля
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class Configuration implements BootstrapInterface
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
        $iterator = new DirectoryIterator(Helper::getRootDir() . DIRECTORY_SEPARATOR . 'config');

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->isReadable()) {
                Config::add($file->getBasename('.php'), @include $file->getPathname());
            }
        }
    }
}