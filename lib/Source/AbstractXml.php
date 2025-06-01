<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;
use EmptyIterator;

/**
 * Базовое представление xml источников данных
 *
 * @internal
 *
 * @package Source
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractXml implements Iterator
{
    use IterableTrait;

    /**
     * Родительский тег элементов
     *
     * @var string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected string $rootTag = 'data';

    /**
     * @param string $path Путь до xml файла
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(protected readonly string $path)
    {
    }

    /**
     * Парсинг xml файла
     *
     * @param mixed $resource
     * @return Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function parsing(mixed $resource): Iterator;

    /**
     * Указание родительского тега элементов
     *
     * Если изменение происходит после формирования указателя({@see self::fetch()}), то он сбрасывается
     *
     * @param string $rootTag
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setRootTag(string $rootTag): self
    {
        $this->rootTag = $rootTag;

        if ($this->iterator) {
            $this->iterator = null;
        }

        return $this;
    }

    /**
     * Загрузка данных источника
     *
     * @return Iterator
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function load(): Iterator
    {
        $resource = fopen($this->path, 'r');

        if (!$resource) {
            return new EmptyIterator();
        }

        return $this->parsing($resource);
    }
}