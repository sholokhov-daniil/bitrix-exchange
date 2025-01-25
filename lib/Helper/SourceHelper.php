<?php

namespace Sholokhov\Exchange\Helper;

use CFile;

class SourceHelper
{
    /**
     * Скачивание источника на сервер.
     * Если источник находится на локальной машине, то нечего не происходит
     *
     * @param string $path
     * @return resource
     */
    public static function download(string $path)
    {
        if (!file_exists($path)) {
            $path = CFile::MakeFileArray($path)['tmp_name'];
        }

        return fopen($path, 'r+');
    }
}