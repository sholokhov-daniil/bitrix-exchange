<?php

namespace Sholokhov\Exchange\UI\View;

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Cache\Builder\EntityCacheBuilder;

/**
 * Создание механизма отрисовки UI
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class ViewBuilder
{
    /**
     * @param string|null $folder
     * @param array $options
     * @return View
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function create(string $folder = null, array $options = []): View
    {
        $folder ??= Helper::getRootDir() . DIRECTORY_SEPARATOR . 'views';
        $view = new View($folder, $options);

        if (!array_key_exists('cache', $options)) {
            $view->setCache(EntityCacheBuilder::build($view::class));
        }

        return $view;
    }
}