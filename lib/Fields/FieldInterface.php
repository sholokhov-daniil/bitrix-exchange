<?php

declare (strict_types=1);

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Preparation\PreparationInterface;

/**
 * Описание настроек свойства
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Field
 */
interface FieldInterface
{
    /**
     * Поле отвечает за идентификацию значений.
     * На основе данного поля происходит определение наличия импортированного значения
     * или обновление существующего.
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isPrimary(): bool;

    /**
     * Свойство отвечает за хранение хэша
     *
     * @return bool
     *
     * @since 1.1.0
     * @version 1.1.0
     */
    public function isHash(): bool;

    /**
     * При отсутствии связующей сущности произвести его создание.
     *
     * @example Описываем свойство типа: привязка к элементу инфоблока. Если значение true, то при отсутствии элемента оно будет создано
     *
     * @return bool
     *
     * @version 1.0.0
     * @version 1.0.0
     */
    public function isCreatedLink(): bool;

    /**
     * Получение пути хранения значения
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getFrom(): string;

    /**
     * Код свойства в которое необходимо записать значение
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getTo(): string;

    /**
     * Цель значения
     *
     * @return ?callable
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getPreparation(): ?callable;

    /**
     * Получение дочернего элемента
     *
     * @return FieldInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getChildren(): ?FieldInterface;

    /**
     * Получение подготовителя значения перед преобразованием на основе настроек сущности
     *
     * @return callable|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getNormalizer(): ?callable;
}