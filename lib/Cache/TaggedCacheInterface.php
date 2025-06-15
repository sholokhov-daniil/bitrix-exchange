<?php

namespace Sholokhov\Exchange\Cache;

/**
 * Структура механизма кеширования по тегу
 *
 * @since 1.2.0
 * @version 1.2.0
 */
interface TaggedCacheInterface
{
    /**
     * Старт кеширования
     *
     * @param string $path
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function start(string $path): void;

    /**
     * Окончание кеширования
     *
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function end(): void;

    /**
     * Очистка кеша по тегу
     *
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function clear(): void;
}