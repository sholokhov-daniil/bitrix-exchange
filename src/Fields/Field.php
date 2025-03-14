<?php

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\Exchange;

/**
 * Описание настроек свойства
 */
interface Field
{
    /**
     * Поле отвечает за идентификацию значений.
     * На основе данного поля происходит определение наличия импортированного значения
     * или обновление существующего.
     *
     * @return bool
     */
    public function isKeyField(): bool;

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
     * @return ?Exchange
     */
    public function getTarget(): ?Exchange;

    /**
     * Значение является множественным
     *
     * @return bool
     */
    public function isMultiple(): bool;

    /**
     * Получение дочернего элемента
     *
     * @return Field|null
     */
    public function getChildren(): ?Field;
}