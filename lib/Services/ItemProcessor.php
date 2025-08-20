<?php

namespace Sholokhov\Exchange\Services;

use Sholokhov\Exchange\Events\EventManager;
use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\MappingExchangeInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Normalizers\ValueNormalizer;

class ItemProcessor
{
    /**
     * Задача обработки значения
     *
     * @var PreparationPipeline
     */
    private readonly PreparationPipeline $pipeline;

    private readonly MappingExchangeInterface $exchange;
    private ?EventManager $events = null;

    public function __construct(MappingExchangeInterface $exchange)
    {
        $this->exchange = $exchange;
        $this->pipeline = new PreparationPipeline(new ValueNormalizer($exchange));
    }
    
    public function process(array $item): DataResultInterface
    {
        $prepared = $this->pipeline->prepare($item, $this->exchange->getMap());
        $this->events?->send(ExchangeEvent::BeforeImportItem->value, $prepared);

        if ($this->exchange->exists($prepared)) {
            $this->events?->send(ExchangeEvent::BeforeUpdate->value, $prepared);
            $result = $this->exchange->update($prepared);
            $this->events?->send(ExchangeEvent::AfterUpdate->value, $prepared, $result);
        } else {
            $this->events?->send(ExchangeEvent::BeforeAdd->value, $prepared);
            $result = $this->exchange->add($prepared);
            $this->events?->send(ExchangeEvent::AfterAdd->value, $prepared, $result);
        }

        $this->events?->send(ExchangeEvent::AfterImportItem->value, $prepared, $result);

        return $result;
    }
    
    public function setEventManager(EventManager $events): self
    {
        $this->events = $events;
        return $this;
    }
}