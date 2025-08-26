<?php

namespace Sholokhov\Exchange\Factory\Exchange;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Processor\Processor;
use Sholokhov\Exchange\Processor\ProcessorInterface;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class ProcessorFactory
{
    /**
     * Создание процесса обмена
     *
     * @param ExchangeInterface $exchange
     * @return ProcessorInterface
     */
    public static function create(ExchangeInterface $exchange): ProcessorInterface
    {
        return self::resolve($exchange) ?: new Processor($exchange);
    }

    /**
     * Получение пользовательского процесса через событие
     *
     * @param ExchangeInterface $exchange
     * @return ProcessorInterface|null
     */
    private static function resolve(ExchangeInterface $exchange): ?ProcessorInterface
    {
        $event = new Event(Helper::getModuleID(), 'onBeforeCreateProcessor', compact('exchange'));
        $event->send();

        foreach ($event->getResults() as $result) {
            if ($result->getType() !== EventResult::SUCCESS) {
                continue;
            }

            $parameters = (array)$result->getParameters();
            $processor = $parameters['processor'] ?? null;

            if ($processor instanceof ProcessorInterface) {
                return $processor;
            }
        }

        return null;
    }
}