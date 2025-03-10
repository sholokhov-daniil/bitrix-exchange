<?php

namespace Sholokhov\Exchange\Target\Bitrix;

use Iterator;
use Throwable;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages;
use Sholokhov\Exchange\Helper\LoggerHelper;

use Bitrix\Main\Type\DateTime as BXDateTime;

use Psr\Log\LoggerAwareTrait;

/**
 * Производит превращение произвольного значения времени в объект {@see DateTime}
 */
class DateTime implements Exchange
{
    use LoggerAwareTrait;

    /**
     * Выполнить обмен данных
     *
     * @param Iterator $source
     * @return Messages\Result
     */
    public function execute(Iterator $source): Messages\Result
    {
        $result = new Messages\Type\DataResult();

        $values = [];

        foreach ($source as $value) {
            try {
                $values[] = new BXDateTime($value);
            } catch (Throwable $throwable) {
                $result->addError(new Messages\Errors\Error(sprintf('Ошибка преобразования значение "%s" в "%s"', $value, DateTime::class)));
                $this->logger?->error(LoggerHelper::exceptionToString($throwable));
            }
        }

        $result->setData($values);

        return $result;
    }
}