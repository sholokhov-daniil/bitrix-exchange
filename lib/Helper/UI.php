<?php

namespace Sholokhov\Exchange\Helper;

use Bitrix\Main\Context;

/**
 * @internal
 * @since 1.2.0
 * @version 1.2.0
 */
class UI
{
    /**
     * Путь до хранения языковых файлов
     *
     * @param string $name
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getLang(string $name): string
    {
        $root = Context::getCurrent()->getServer()->getDocumentRoot();
        return str_replace($root, '', self::getRootFolder($name) . 'options.php');
    }

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
        $path = self::getRootFolder($name) . 'dist' . DIRECTORY_SEPARATOR . $folder;
        $sitePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);

        return array_map(
            fn(string $fileName) => $sitePath . DIRECTORY_SEPARATOR . $fileName,
            is_dir($path) ? array_diff(scandir($path), ['.', '..']) : []
        );
    }

    /**
     * Путь до директории расширения
     *
     * @param string $name
     * @return string
     *
     * @since 1.2.0
     * @version 1.2.0
     */
    public static function getRootFolder(string $name): string
    {
        return Helper::getRootDir() . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
    }
}