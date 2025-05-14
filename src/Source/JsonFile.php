<?php

namespace Sholokhov\BitrixExchange\Source;

use Sholokhov\BitrixExchange\Helper\IO;

/**
 * Источник данных на основе json файла
 *
 * @internal Наследуемся на свой страх и риск
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class JsonFile extends Json
{
    /**
     * @param string $path Место размещения json файла (локально или удаленно)
     * @param string $sourceKey Ключ из которого необходимо брать данные. Если не указать, что подгружаются все данные
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $path, string $sourceKey = '')
    {
        parent::__construct(IO::getFileContent($path), $sourceKey);
    }
}