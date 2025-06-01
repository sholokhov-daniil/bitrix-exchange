<?php

namespace Sholokhov\BitrixExchange\Fields\Catalog;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * Структура свойства каталога
 *
 * @since 1.0.0
 * @version 1.0.0
 */
interface CatalogFieldInterface extends FieldInterface
{
    /**
     * Значение является ценой
     *
     * @return bool
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isQuantity(): bool;
}
