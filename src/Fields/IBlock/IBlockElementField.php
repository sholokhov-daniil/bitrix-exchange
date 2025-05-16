<?php

namespace Sholokhov\BitrixExchange\Fields\IBlock;

use Sholokhov\BitrixExchange\Fields\Field;

/**
 * Описание свойства элемента ИБ
 *
 * @version 1.0.0
 * @package Field
 */
class IBlockElementField extends Field implements ElementFieldInterface
{
    /**
     * Является свойством
     *
     * @param bool $isProperty
     * @return $this
     */
    public function setProperty(bool $isProperty = true): self
    {
        $this->getContainer()->set('property', $isProperty);
        return $this;
    }

    /**
     * Является свойством или нет
     *
     * @return bool
     */
    public function isProperty(): bool
    {
        return $this->getContainer()->get('property', false);
    }
}