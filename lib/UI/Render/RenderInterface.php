<?php

namespace Sholokhov\Exchange\UI\Render;

/**
 * @since 1.2.0
 * @version 1.2.0
 */
interface RenderInterface
{
    /**
     * Отобразить интерфейс
     *
     * @param mixed $data
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function render(mixed $data): void;
}