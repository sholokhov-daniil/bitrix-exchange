<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Fields\FieldInterface;

/**
 * Структура обмена согласно пользовательской карте
 */
interface MappingExchangeInterface extends ExchangeInterface
{
    /**
     * Карта обмена
     *
     * @return array
     */
    public function getMap(): array;

    /**
     * Указание карты обмена данных
     *
     * @param FieldInterface[] $map
     * @return $this
     */
    public function setMap(array $map): static;
}