<?php

namespace Sholokhov\Exchange\UI\DTO;

/**
 * Описание отображаемого HTML элемента
 *
 * @since 1.2.0
 * @version 1.2.0
 */
interface UIFieldInterface
{
    /**
     * Преобразование настроек в массив, который воспринимает сборщик ui
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function toArray(): array;

    /**
     * Формат отображения поля
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getView(): string;

    /**
     * Уникальное код поля в рамках группы в которой выводится
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getName(): string;

    /**
     * Наименование свойства - отображается в UI
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTitle(): string;

    /**
     * Конфигурация отображения поля
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getOptions(): array;
}