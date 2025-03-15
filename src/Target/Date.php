<?php

namespace Sholokhov\Exchange\Target;

use Throwable;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Messages;
use Sholokhov\Exchange\Messages\ResultInterface;
use Bitrix\Main\Error;
use Sholokhov\Exchange\Helper\LoggerHelper;

use Bitrix\Main\Type\Date as BXDate;

use Psr\Log\LoggerAwareTrait;

/**
 * Производит превращение произвольного значения времени в объект {@see Date}
 */
class Date implements ExchangeInterface
{
    use LoggerAwareTrait;

    /**
     * Выполнить обмен данных
     *
     * @param \Iterator $source
     * @return ResultInterface
     */
    public function execute(\Iterator $source): Messages\ResultInterface
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