<?php

namespace Sholokhov\BitrixExchange\Helper;

use Bitrix\Main\Application;

class Site
{
    /**
     * Получение текущего языка
     *
     * @return string
     */
    public static function getLanguage(): string
    {
        return Application::getInstance()->getContext()->getLanguage() ?: 'ru';
    }
}