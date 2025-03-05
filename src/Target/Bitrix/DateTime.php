<?php

namespace Sholokhov\Exchange\Target\Bitrix;

use Throwable;

use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Helper\LoggerHelper;
use Sholokhov\Exchange\Target\TargetInterface;
use Sholokhov\Exchange\Source\SourceAwareTrait;

use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime as BXDateTime;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Производит превращение произвольного значения времени в объект {@see DateTime}
 */
class DateTime implements TargetInterface, LoggerAwareInterface
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
                $values[] = new BXDateTime($value);
            } catch (Throwable $throwable) {
                $result->addError(new Error(sprintf('Ошибка преобразования значение "%s" в "%s"', $value, DateTime::class)));
                $this->logger?->error(LoggerHelper::exceptionToString($throwable));
            }
        }

        $result->setData($values);

        return $result;
    }
}