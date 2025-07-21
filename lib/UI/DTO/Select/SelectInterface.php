<?php

namespace Sholokhov\Exchange\UI\DTO\Select;

use Sholokhov\Exchange\UI\DTO\UIFieldInterface;

/**
 * Структура списка значений
 *
 * @since 1.2.0
 * @version 1.2.0
 */
interface SelectInterface extends UIFieldInterface
{
    /**
     * Получение списка значений
     *
     * @return EnumValueInterface[]
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getEnums(): array;

    /**
     * Добавление значения списка
     *
     * @param EnumValueInterface $enum
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function addEnum(EnumValueInterface $enum): static;
}