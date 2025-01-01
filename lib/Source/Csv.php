<?php

namespace Sholokhov\Exchange\Source;

use Exception;
use Bitrix\Main\Text\Encoding;

/**
 * Источник данных на csv файла
 *
 * @internal Наследуемся на свой страх и риск
 */
class Csv implements SourceInterface
{
    /** @var resource|null  */
    private $resource = null;

    /**
     * Символ-разделитель полей
     *
     * @var string
     */
    private string $separator = ',';

    /**
     * Устанавливает символ-ограничитель значения поля и принимает только один однобайтовый символ
     *
     * @var string
     */
    private string $enclosure = "\"";

    /**
     * Устанавливает символ экранирования и принимает только один однобайтовый символ или пустую строку.
     * Пустая строка "" отключает внутренний механизм экранирования
     *
     * @var string
     */
    private string $escape = "\\";

    /**
     * Устанавливают значение, которое больше самой длинной строки в CSV-файле, иначе строка разбивается на части заданной длины,
     * если только место разделения не встретится внутри символов-ограничителей.
     * Длина строк измеряется в символах с учётом символов конца строки, которыми завершаются строки.
     *
     * @var int|null
     */
    private ?int $length = null;


    public function __construct(
        private readonly string $path,
        private readonly string $encoding = 'UTF-8',
    )
    {
        $this->resource = fopen($this->path, 'r');

        if (!$this->resource) {
            throw new Exception('Unable to open csv file');
        }
    }

    public function __destruct()
    {
        fclose($this->resource);
    }

    /**
     * Получение следующего значения csv файла
     *
     * @return array|null
     */
    public function fetch(): ?array
    {
        $current = fgetcsv($this->resource, $this->length, $this->separator, $this->enclosure, $this->escape);

        if (is_array($current) && $this->encoding <> SITE_CHARSET) {
            $current = Encoding::convertEncoding($current, $this->encoding, SITE_CHARSET);
        }

        return is_array($current) ? $current : null;
    }

    /**
     * Устанавливают значение, которое больше самой длинной строки в CSV-файле,
     * иначе строка разбивается на части заданной длины, если только место разделения не встретится внутри символов-ограничителей.
     * Длина строк измеряется в символах с учётом символов конца строки, которыми завершаются строки.
     *
     * @see fgetcsv
     *
     * @param int $length
     * @return $this
     */
    public function setLength(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Символ-разделитель полей и принимает только один однобайтовый символ
     *
     * @see fgetcsv
     *
     * @param string $separator
     * @return $this
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * Устанавливает символ-ограничитель значения поля и принимает только один однобайтовый символ
     *
     * @see fgetcsv
     *
     * @param string $enclosure
     * @return $this
     */
    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;
        return $this;
    }

    /**
     * Устанавливает символ экранирования и принимает только один однобайтовый символ или пустую строку.
     * Пустая строка "" отключает внутренний механизм экранирования
     *
     * @param string $escape
     * @return $this
     */
    public function setEscape(string $escape): self
    {
        $this->escape = $escape;
        return $this;
    }
}