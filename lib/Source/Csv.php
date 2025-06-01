<?php

namespace Sholokhov\Exchange\Source;

use SplFileObject;

/**
 * Источник данных на csv файла
 *
 * @internal Наследуемся на свой страх и риск
 *
 * @package Source
 * @since 1.0.0
 * @version 1.0.0
 */
class Csv implements \Iterator
{
    private SplFileObject $file;

    /**
     * @param string $path Путь до файла
     * @param string $encoding Кодировка файла
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(
        private readonly string $path,
        private readonly string $encoding = 'UTF-8',
    )
    {
        $this->file = new SplFileObject($this->path);
    }

    /**
     * Получение текущего значения
     *
     * @return array|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function current(): ?array
    {
        if (!$this->valid()) {
            return null;
        }

        $lastLine = $this->file->key();
        $data = $this->file->fgetcsv(...$this->file->getCsvControl());
        $this->file->seek($lastLine);

        return is_array($data) ? $data : null;
    }

    /**
     * Устанавливают значение, которое больше самой длинной строки в CSV-файле,
     * иначе строка разбивается на части заданной длины, если только место разделения не встретится внутри символов-ограничителей.
     * Длина строк измеряется в символах с учётом символов конца строки, которыми завершаются строки.
     *
     * @param int $length
     * @return $this
     *
     * @see fgetcsv
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setLength(int $length): self
    {
        $this->file->setMaxLineLen($length);
        return $this;
    }

    /**
     * Символ-разделитель полей и принимает только один однобайтовый символ
     *
     * @param string $separator
     * @return $this
     *
     * @see fgetcsv
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setSeparator(string $separator): self
    {
        $this->file->setCsvControl($separator);
        return $this;
    }

    /**
     * Устанавливает символ-ограничитель значения поля и принимает только один однобайтовый символ
     *
     * @param string $enclosure
     * @return $this
     *
     * @see fgetcsv
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setEnclosure(string $enclosure): self
    {
        $control = $this->file->getCsvControl();
        $this->file->setCsvControl($control[0], $enclosure, $control[1]);

        return $this;
    }

    /**
     * Устанавливает символ экранирования и принимает только один однобайтовый символ или пустую строку.
     * Пустая строка "" отключает внутренний механизм экранирования
     *
     * @param string $escape
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setEscape(string $escape): self
    {
        $control = $this->file->getCsvControl();
        $this->file->setCsvControl($control[0], $control[1], $escape);
        return $this;
    }

    /**
     * Последняя позиция указателя в файле
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function next(): void
    {
        $this->file->next();
    }

    /**
     * Позиция указателя в файле
     *
     * @return int|false
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function key(): int|false
    {
        return $this->file->ftell();
    }

    /**
     * Флаг окончания потока данных
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function valid(): bool
    {
        return $this->file->valid();
    }

    /**
     * Поместить указатель в начало файла
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function rewind(): void
    {
        $this->file->rewind();
    }
}