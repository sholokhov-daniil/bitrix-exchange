<?php

namespace Sholokhov\BitrixExchange\Fields\Catalog;

use Sholokhov\Exchange\Fields\FieldInterface;

interface PriceFieldInterface extends FieldInterface
{
    /**
     * Валюта
     *
     * @return string
     */
    public function getCurrency(): string;
}