<?php

namespace Sholokhov\Exchange\Services\Map;

use Sholokhov\Exchange\Fields\FieldInterface;

/**
 * Хранилище правил соответствия полей обмена
 */
interface MappingRegistryInterface
{
    /**
     * Указание карты обмена
     *
     * @param array $map
     * @return $this
     */
    public function setMap(array $map): static;

    /**
     * Получение карты обмена
     *
     * @return array
     */
    public function getMap(): array;

    /**
     * Получение свойства отвечающего за идентификацию значения
     *
     * @return FieldInterface|null
     */
    public function getPrimaryField(): ?FieldInterface;

    /**
     * Получение поля отвечающего за хеш обмена
     *
     * @return FieldInterface|null
     */
    public function getHashField(): ?FieldInterface;
}