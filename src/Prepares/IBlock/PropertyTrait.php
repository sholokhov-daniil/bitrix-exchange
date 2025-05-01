<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock;

use Sholokhov\BitrixExchange\Repository\IBlock\PropertyRepository;

trait PropertyTrait
{
    /**
     * Хранилище информации о свойствах
     *
     * @var PropertyRepository
     */
    private PropertyRepository $repository;

    /**
     * ID информационного блока, для которого необходимо подгрузить информацию о свойствах
     *
     * @var int
     */
    protected readonly int $iblockId;

    /**
     * Получение хранилища
     *
     * @return PropertyRepository
     */
    final protected function getRepository(): PropertyRepository
    {
        return $this->repository ??= new PropertyRepository($this->iblockId);
    }
}