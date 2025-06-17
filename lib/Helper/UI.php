<?php

namespace Sholokhov\Exchange\Helper;

use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
class UI
{
    /**
     * Получение доступных JS файлов расширения
     *
     * @param string $name
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getJs(string $name): array
    {
        return self::getFiles($name, 'js');
    }

    /**
     * Получение доступных стилей расширения
     *
     * @param string $name
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getCss(string $name): array
    {
        return self::getFiles($name, 'css');
    }

    /**
     * Получение доступных файлов расширения
     *
     * @param string $name
     * @param string $folder
     * @return array
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getFiles(string $name, string $folder): array
    {
        $path = Helper::getRootDir() . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . $folder;
        $sitePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);

        return array_map(
            fn(string $fileName) => $sitePath . DIRECTORY_SEPARATOR . $fileName,
            is_dir($path) ? array_diff(scandir($path), ['.', '..']) : []
        );
    }
}