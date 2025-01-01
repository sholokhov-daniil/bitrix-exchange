<?php

namespace Sholokhov\Exchange\Source;

/**
 * Источник данных на основе json файла
 *
 * @internal Наследуемся на свой страх и риск
 */
class JsonFile extends Json
{
    /**
     * @param string $path Место размещения json файла (локально или удаленно)
     * @param string $sourceKey Ключ из которого необходимо брать данные. Если не указать, что подгружаются все данные
     */
    public function __construct(string $path, string $sourceKey = '')
    {
        parent::__construct($this->getContent($path), $sourceKey);
    }

    /**
     * Получение содержимого файла
     *
     * @param string $path
     * @return string
     */
    private function getContent(string $path): string
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