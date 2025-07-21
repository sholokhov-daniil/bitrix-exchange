<?php

namespace Sholokhov\Exchange\Repository\Types;

trait MemoryTrait
{
    /**
     * Хранилище данных
     *
     * @var Memory
     * @since 1.2.0
     * @version 1.2.0
     */
    private readonly Memory $repository;

    /**
     * Получение хранилища данных
     *
     * @return Memory
     * @since 1.2.0
     * @version 1.2.0
     */
    protected function getRepository(): Memory
    {
        return $this->repository ??= new Memory;
    }
}