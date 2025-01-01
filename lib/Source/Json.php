<?php

namespace Sholokhov\Exchange\Source;

/**
 * Источник данных на основе json строки
 *
 * @internal Наследуемся на свой страх и риск
 */
class Json implements SourceInterface
{
    /**
     * JSON хранит множественное значение
     *
     * @var bool
     */
    private bool $multiple = false;

    private SourceInterface $iterator;

    /**
     * @param string $json JSON строка
     * @param string|int|null $sourceKey Ключ из которого необходимо брать данные. Если не указать, что подгружаются все данные
     */
    public function __construct(
        private readonly string $json,
        private readonly string|int|null $sourceKey = null,
    )
    {
    }

    public function fetch(): mixed
    {
        $this->iterator ??= $this->loadIterator();
        return $this->iterator->fetch();
    }

    /**
     * Значение является можественным
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Указание формата данных (множественное или один элемент)
     *
     * @param bool $multiple
     * @return $this
     */
    public function setMultiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Загрузка данных
     *
     * @return SourceInterface
     */
    private function loadIterator(): SourceInterface
    {
        $data = $this->loadData();
        return $this->multiple && is_array($data) ? new Items($data) : new Item($data);
    }

    /**
     * Загрузка данных из json файла
     *
     * @return mixed
     */
    private function loadData(): mixed
    {
        if (!json_validate($this->json)) {
            return null;
        }

        $data = json_decode($this->json, true);

        if ($this->sourceKey !== null) {
            if (is_array($data)) {
                $data = $data[$this->sourceKey];
            } else {
                $data = null;
            }
        }

        return $data;
    }
}