<?php

namespace Sholokhov\Exchange\Builder;

use CUser;
use Sholokhov\Exchange\Helper\Config;

/**
 * Сборщик меню модуля
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class ModuleMenu
{
    /**
     * @var CUser
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    private CUser $user;

    /**
     * @since 1.2.0
     * @version 1.2.0
     */
    public function __construct()
    {
        $this->user = new CUser;
    }

    /**
     * Сборка меню
     *
     * @param array $globalMenu
     * @return void
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public function make(array &$globalMenu): void
    {
        if (!$this->user->IsAdmin()) {
            return;
        }

        $globalMenu = array_merge($globalMenu, Config::get('menu'));
    }
}