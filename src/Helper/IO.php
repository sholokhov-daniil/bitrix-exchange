<?php

namespace Sholokhov\BitrixExchange\Helper;

/**
 * @package Helper
 * @since 1.0.0
 * @version 1.0.0
 */
class IO
{
    /**
     * Получение содержимого файла
     *
     * @param string $path
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function getFileContent(string $path): string
    {
        $contents = '';

        $resource = fopen($path, 'rb');
        if (!$resource) {
            return $contents;
        }

        while (!feof($resource)) {
            $contents .= fread($resource, 8192);
        }

        fclose($resource);

        return $contents;
    }
}