<?php

namespace Sholokhov\BitrixExchange\Messages\Type;

use Sholokhov\BitrixExchange\Messages\DataResultInterface;

/**
 * Результата работы с произвольными данными
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class DataResult extends Result implements DataResultInterface
{
    /**
     * @var mixed|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected mixed $data = null;

    /**
     * Установка результата выполнения
     *
     * @param mixed $data
     * @return DataResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setData(mixed $data): DataResultInterface
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Получение дополнительной информации результата выполнения
     *
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getData(): mixed
    {
        return $this->data;
    }
}