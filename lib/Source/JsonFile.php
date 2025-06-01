<?php

namespace Sholokhov\Exchange\Source;

use Sholokhov\Exchange\Helper\IO;

/**
 * Источник данных на основе json файла
 *
 *
 * @package Source
 * @since 1.0.0
 * @version 1.0.0
 */
class JsonFile extends Json
{
    /**
     * @param string $path Место размещения json файла (локально или удаленно)
     * @param array $options
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $path, array $options = [])
    {
        parent::__construct(IO::getFileContent($path), $options);
    }
}