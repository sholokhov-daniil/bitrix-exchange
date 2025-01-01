<?php

namespace Sholokhov\Exchange\Source;

/**
 * Источник данных на основе сериализованной строки
 *
 * @internal Наследуемся на свой страх и риск
 */
class SerializeItem implements SourceInterface
{
    private SourceInterface $iterator;

    /**
     * @param string $data Строка с данными
     * @param bool $multiple Данные являются множественными
     */
    public function __construct(
        private readonly string $data,
        private readonly bool $multiple = true,
    )
    {
    }

    public function fetch(): mixed
    {
        $this->iterator ??= $this->load();
        return $this->iterator->fetch();
    }

    /**
     * Инициализация итератора данных из сериализованной строки
     *
     * @return SourceInterface
     */
    protected function load(): SourceInterface
    {
        $data = unserialize($this->data);
        return $this->multiple && is_array($data) ? new Items($data) : new Item($data);
    }
}