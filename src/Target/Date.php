<?php

namespace Sholokhov\Exchange\Target;

use Throwable;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Errors\Error;
use Sholokhov\Exchange\Helper\LoggerHelper;

use Bitrix\Main\Type\Date as BXDate;

use Psr\Log\LoggerAwareTrait;

/**
 * Производит превращение произвольного значения времени в объект {@see Date}
 */
class Date implements Exchange
{
    use LoggerAwareTrait;

    /**
     * Выполнить обмен данных
     *
     * @param \Iterator $source
     * @return Result
     */
    public function execute(\Iterator $source): Messages\Result
    {
        $result = new Messages\Type\DataResult;
        $values = [];

        foreach ($source as $value) {
            try {
                $values[] = new BXDate($value);
            } catch (Throwable $throwable) {
                $result->addError(new Error(sprintf('Ошибка преобразования значение "%s" в "%s"', $value, Date::class)));
                $this->logger?->error(LoggerHelper::exceptionToString($throwable));
            }
        }

        $result->setData($values);

        return $result;
    }
}