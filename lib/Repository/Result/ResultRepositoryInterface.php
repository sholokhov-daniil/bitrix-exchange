<?php

namespace Sholokhov\BitrixExchange\Repository\Result;

use Stringable;

/**
 * Хранилище результата обмена
 *
 * @since 1.0.0
 * @version 1.0.0
 */
interface ResultRepositoryInterface
{
    /**
     * Получение элементов, которые приняли участие в обмене
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function get(): mixed;

    /**
     * Добавление значение в хранилище
     *
     * @param Stringable|string $value Добавляемое значение в хранилище
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function add(Stringable|string $value): void;
}