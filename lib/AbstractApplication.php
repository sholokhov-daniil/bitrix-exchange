<?php

namespace Sholokhov\Exchange;

use Throwable;

use Sholokhov\Exchange\Helper\LoggerHelper;

use Bitrix\Main\Error;
use Bitrix\Main\Result;

use Psr\Log\LoggerAwareTrait;

abstract class AbstractApplication implements ExchangeInterface
{
    use LoggerAwareTrait;

    private Result $result;

    /**
     * Основная логика импорта\парсинга
     *
     * @return void
     */
    abstract protected function logic(): void;

    /**
     * Запуск механизма импорт\парсинга
     *
     * @final
     * @return Result
     */
    final public function run(): Result
    {
        try {
            // Событие перед началом работы
            $this->logic();
            // Событие после окончания импорта
        } catch (Throwable $throwable) {
            $this->result->addError(new Error($throwable->getMessage(), $throwable->getCode()));
            $this->logger?->critical(LoggerHelper::exceptionToString($throwable));
        }

        if ($this->logger) {
            foreach ($this->getResult()->getErrors() as $error) {
                $this->logger->error($error->getMessage());
            }
        }


        return $this->getResult();
    }

    /**
     * Результат работы
     *
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result ??= new Result();
    }
}