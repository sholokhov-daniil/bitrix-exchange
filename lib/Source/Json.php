<?php

namespace Sholokhov\Exchange\Source;

use Iterator;
use ArrayIterator;

/**
 * Источник данных на основе json файла
 *
 * @internal Наследуемся на свой страх и риск
 */
class Json extends AbstractSource
{
    /**
     * @param string $path Место размещения json файла (локально или удаленно)
     * @param string $sourceKey Ключ из которого необходимо брать данные. Если не указать, что подгружаются все данные
     */
    public function __construct(
        private readonly string $path,
        private readonly string $sourceKey = ''
    )
    {
    }

    /**
     * Загрузка данных
     *
     * @return Iterator
     */
    protected function load(): Iterator
    {
        $data = $this->loadData();
        return is_array($data) ? new ArrayIterator($data) : new Item($data);
    }

    /**
     * Загрузка данных из json файла
     *
     * @return mixed
     */
    private function loadData(): mixed
    {
        $json = $this->getContent();

        if (!json_validate($json)) {
            return [];
        }

        $data = (array)json_decode($json, true);

        if ($this->sourceKey) {
            $data = $data[$this->sourceKey] ?? [];
        }

        return $data;
    }

    /**
     * Получение содержимого файла
     *
     * @return string
     */
    private function getContent(): string
    {
        $contents = '';

        $resource = fopen($this->path, 'rb');
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