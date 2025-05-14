<?php

namespace Sholokhov\BitrixExchange\Fields\IBlock;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * Поле описывающее свойство элемента ИБ
 *
 * @version 1.0.0
 */
interface ElementFieldInterface extends FieldInterface
{
    /**
     * Является свойством
     *
     * @return bool
     */
    public function isProperty(): bool;
}