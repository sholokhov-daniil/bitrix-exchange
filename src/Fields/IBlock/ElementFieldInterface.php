<?php

namespace Sholokhov\Exchange\Fields\IBlock;

use Sholokhov\Exchange\Fields\FieldInterface;

/**
 * Поле описывающее свойство элемента ИБ
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