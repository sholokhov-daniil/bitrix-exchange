<?php

namespace Sholokhov\Exchange\UI\DTO\Select;

interface EnumValueInterface
{
    /**
     * Значение элемента списка
     *
     * @return mixed
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getValue(): mixed;

    /**
     * Текстовое описание значения
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTitle(): string;
}