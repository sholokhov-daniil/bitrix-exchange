<?php

namespace Sholokhov\BitrixExchange\Preparation\UserField;

use Sholokhov\BitrixExchange\Repository\Fields\UFRepository;

/**
 * @package Preparation
 * @version 1.0.0
 * @since 1.0.0
 */
trait UFTrait
{
    /**
     * @var string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected readonly string $entityId;

    /**
     * Хранилище информации о свойствах
     *
     * @var UFRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private UFRepository $repository;

    /**
     * Получения хранилища информации свойств
     *
     * @return UFRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    final protected function getFieldRepository(): UFRepository
    {
        return $this->repository ??= new UFRepository(['entity_id' => $this->entityId]);
    }
}