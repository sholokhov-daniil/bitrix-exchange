<?php

namespace Sholokhov\Exchange\Source;

use EmptyIterator;
use Iterator;
use Sholokhov\Exchange\Helper\SourceHelper;

/**
 * Базовое представление xml источников данных
 *
 * @internal
 */
abstract class AbstractXml implements SourceInterface
{
    /**
     * Значения источника данных
     *
     * @var Iterator|null
     */
    private ?Iterator $iterator = null;

    /**
     * Родительский тег элементов
     *
     * @var string
     */
    protected string $rootTag = 'data';

    /**
     * @param string $path Путь до xml файла
     */
    public function __construct(protected readonly string $path)
    {
    }

    /**
     * Парсинг xml файла
     *
     * @param mixed $resource
     * @return Iterator
     */
    abstract protected function parsing(mixed $resource): Iterator;

    /**
     * Получение xml элемента
     *
     * @return array
     */
    public function fetch(): array
    {
        $this->iterator ??= $this->load();

        if (!$this->iterator->valid()) {
            return [];
        }

        $value = $this->iterator->current();
        $this->iterator->next();

        return $this->prepareValue($value);
    }

    /**
     * Указание родительского тега элементов
     *
     * Если изменение происходит после формирования указателя({@see self::fetch()}), то он сбрасывается
     *
     * @param string $rootTag
     * @return $this
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
     */
    final protected function load(): Iterator
    {
        $resource = SourceHelper::download($this->path);

        if (!$resource) {
            return new EmptyIterator();
        }

        return $this->parsing($resource);
    }

    /**
     * Преобразование значения в валидный формат
     *
     * @param array $value
     * @return array
     */
    protected function prepareValue(array $value): array
    {
        return $value;
    }
}