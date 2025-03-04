<?php

namespace Sholokhov\Exchange\Target\Bitrix;

use Throwable;

use Sholokhov\Exchange\Result;
use Sholokhov\Exchange\ResultInterface;
use Sholokhov\Exchange\Helper\LoggerHelper;
use Sholokhov\Exchange\Target\TargetInterface;
use Sholokhov\Exchange\Source\SourceAwareTrait;

use Bitrix\Main\Error;
use Bitrix\Main\Type\Date as BXDate;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Производит превращение произвольного значения времени в объект {@see Date}
 */
class Date implements TargetInterface, LoggerAwareInterface
{
    use SourceAwareTrait, LoggerAwareTrait;

    /**
     * Выполнить обмен данных
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = new Result;
        $values = [];

        while ($value = $this->source->fetch()) {
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