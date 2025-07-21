<?php

namespace Sholokhov\Exchange\UI\DTO;

/**
 * API настройки
 *
 * @since 1.2.0
 * @version 1.2.0
 */
interface ApiInterface
{
    /**
     * Контроллер на который идет запрос
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getAction(): string;

    /**
     * Параметры запроса
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getParameters(): array;

    /**
     * JS обработчик результата API ответа
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getCallback(): string;
}