<?php

namespace Sholokhov\Exchange\Factory\Exchange;

use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Services\FieldPreparationPipeline;
use Sholokhov\Exchange\Preparation\FieldPreparationPipelineInterface;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;


class FieldPreparationPipelineFactory
{
    /**
     * Создание процесса обмена
     *
     * @param ExchangeInterface $exchange
     * @return FieldPreparationPipelineInterface
     */
    public static function create(ExchangeInterface $exchange): FieldPreparationPipelineInterface
    {
        return self::resolve($exchange) ?: new FieldPreparationPipeline(ValueNormalizerFactory::create($exchange));
    }

    /**
     * Получение пользовательского процесса через событие
     *
     * @param ExchangeInterface $exchange
     * @return FieldPreparationPipelineInterface|null
     */
    private static function resolve(ExchangeInterface $exchange): ?FieldPreparationPipelineInterface
    {
        $event = new Event(Helper::getModuleID(), 'onBeforeCreatePreparationPipeline', compact('exchange'));
        $event->send();

        foreach ($event->getResults() as $result) {
            if ($result->getType() !== EventResult::SUCCESS) {
                continue;
            }

            $parameters = (array)$result->getParameters();
            $entity = $parameters['entity'] ?? null;

            if ($entity instanceof FieldPreparationPipelineInterface) {
                return $entity;
            }
        }

        return null;
    }
}