<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock;

use Sholokhov\BitrixExchange\Repository\IBlock\PropertyRepository;

/**
 * @version 1.0.0
 * @since 1.0.0
 */
trait PropertyTrait
{
    /**
     * Хранилище информации о свойствах
     *
     * @var PropertyRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    private PropertyRepository $repository;

    /**
     * ID информационного блока, для которого необходимо подгрузить информацию о свойствах
     *
     * @var int
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected readonly int $iblockId;

    /**
     * Получение хранилища
     *
     * @return PropertyRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    final protected function getRepository(): PropertyRepository
    {
        return $this->repository ??= new PropertyRepository($this->iblockId);
    }
}