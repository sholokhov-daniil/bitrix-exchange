<?php

namespace Sholokhov\Exchange\Processor;

use Sholokhov\Exchange\Messages\ExchangeResultInterface;

interface ProcessorInterface
{
    /**
     * Запуск процесса обработки элемента
     *
     * @param iterable $source
     * @param ExchangeResultInterface $result
     * @return void
     */
    public function run(iterable $source, ExchangeResultInterface $result): void;
}