<?php

namespace Sholokhov\Exchange\UI\DTO;

interface ApiAvailableInterface
{
    /**
     * Получение API настроек
     *
     * @return ApiInterface
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getApi(): ApiInterface;
}