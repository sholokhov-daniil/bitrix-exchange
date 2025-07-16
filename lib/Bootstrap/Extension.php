<?php

namespace Sholokhov\Exchange\Bootstrap;

use CJSCore;
use Sholokhov\Exchange\Helper\Config;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
class Extension implements BootstrapInterface
{
    /**
     * Выполнить загрузку
     *
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function bootstrap(): void
    {
        $iterator = Config::get('extension') ?: [];

        if (!is_array($iterator)) {
            return;
        }

        array_walk($iterator, fn($options, $name) => CJSCore::RegisterExt($name, $options));
    }
}