<?php

namespace Sholokhov\Exchange\Bootstrap;

/**
 * Производит инициализацию модуля
 *
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
interface BootstrapInterface
{
    /**
     * Запуск загрузки
     *
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function bootstrap(): void;
}