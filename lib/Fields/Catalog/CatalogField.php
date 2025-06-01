<?php

namespace Sholokhov\BitrixExchange\Fields\Catalog;

use Sholokhov\BitrixExchange\Fields\Field;

/**
 * Описание свойства каталога
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class CatalogField extends Field implements CatalogFieldInterface
{
    /**
     * Свойство хранит цену
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isQuantity(): bool
    {
        return $this->getContainer()->get('quantity', false);
    }

    /**
     * Свойство хранит цену
     *
     * @param bool $quantity
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setQuantity(bool $quantity = true): self
    {
        $this->getContainer()->set('quantity', $quantity);
        return $this;
    }
}
