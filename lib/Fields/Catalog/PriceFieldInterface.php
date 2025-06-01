<?php

namespace Sholokhov\Exchange\Fields\Catalog;

use Sholokhov\Exchange\Fields\FieldInterface;

/**
 * @version 1.0.0
 * @package Field
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