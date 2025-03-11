<?php

namespace Sholokhov\Exchange\Fields\IBlock;

use Sholokhov\Exchange\Fields\BaseField;

/**
 * Описание свойства элемента ИБ
 */
class IBlockElementField extends BaseField implements ElementField
{
    /**
     * Является свойством
     *
     * @param bool $isProperty
     * @return $this
     */
    public function setProperty(bool $isProperty = true): self
    {
        $this->getContainer()->setField('property', $isProperty);
        return $this;
    }

    /**
     * Является свойством или нет
     *
     * @return bool
     */
    public function isProperty(): bool
    {
        return $this->getContainer()->getField('property', false);
    }
}