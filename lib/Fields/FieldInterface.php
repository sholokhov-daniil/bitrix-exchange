<?php

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\Target\TargetInterface;

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
     * @return ?TargetInterface
     */
    public function getTarget(): ?TargetInterface;

    /**
     * Значение является множественным
     *
     * @return bool
     */
    public function isMultiple(): bool;

    /**
     * Получение дочернего элемента
     *
     * @return FieldInterface|null
     */
    public function getChildren(): ?FieldInterface;
}