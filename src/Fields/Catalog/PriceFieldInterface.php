<?php

namespace Sholokhov\BitrixExchange\Fields\Catalog;

use Sholokhov\BitrixExchange\Fields\FieldInterface;

/**
 * @version 1.0.0
 */
interface PriceFieldInterface extends FieldInterface
{
    /**
     * Валюта
     *
     * @return string
     */
    public function getCurrency(): string;
}