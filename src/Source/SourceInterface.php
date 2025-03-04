<?php

namespace Sholokhov\Exchange\Source;

/**
 * Источник данных импорта
 */
interface SourceInterface
{
    /**
     * Получение значения из стака.
     * После вызова каретка смещается на следующий элемент
     *
     * @return mixed
     */
    public function fetch(): mixed;
}