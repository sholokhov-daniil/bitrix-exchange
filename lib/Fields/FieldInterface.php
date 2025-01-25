<?php

namespace Sholokhov\Exchange\Fields;

/**
 * Описание настроек свойства
 */
interface FieldInterface
{
    /**
     * Получение пути хранения значения
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Код свойства в которое необходимо записать значение
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Цель значения
     *
     * @return string
     */
    public function getTarget(): string;

    /**
     * Значение является множественным
     *
     * @return bool
     */
    public function isMultiple(): bool;
}