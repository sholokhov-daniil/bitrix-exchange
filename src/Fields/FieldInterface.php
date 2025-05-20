<?php

declare (strict_types=1);

namespace Sholokhov\BitrixExchange\Fields;

use Sholokhov\BitrixExchange\ExchangeInterface;

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
    public function getPath(): string;

    /**
     * Код свойства в которое необходимо записать значение
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getCode(): string;

    /**
     * Цель значения
     *
     * @return ?ExchangeInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getTarget(): ?ExchangeInterface;

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
     * Получение валидаторов значения свойства
     *
     * @return callable[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getNormalizers(): array;
}