<?php

namespace Sholokhov\BitrixExchange\Messages;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
interface DataResultInterface extends ResultInterface
{
    /**
     * Установка значения результата
     *
     * @param mixed $data
     * @return self
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setData(mixed $data): self;

    /**
     * Получение данных результата действия
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getData(): mixed;
}