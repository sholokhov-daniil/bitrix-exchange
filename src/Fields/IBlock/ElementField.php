<?php

namespace Sholokhov\Exchange\Fields\IBlock;

use Sholokhov\Exchange\Fields\Field;

/**
 * Поле описывающее свойство элемента ИБ
 */
interface ElementField extends Field
{
    /**
     * Является свойством
     *
     * @return bool
     */
    public function isProperty(): bool;
}