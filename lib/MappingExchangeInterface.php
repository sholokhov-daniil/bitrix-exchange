<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Fields\FieldInterface;

/**
 * Структура обмена согласно пользовательской карте
 *
 * @since 1.0.0
 * @version 1.0.0
 */
interface MappingExchangeInterface extends ExchangeInterface
{
    /**
     * Указание карты обмена данных
     *
     * @param FieldInterface[] $map
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setMap(array $map): static;
}