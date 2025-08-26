<?php

namespace Sholokhov\Exchange\Processor;

use Throwable;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Dispatcher\ExchangeEventDispatcher;
use Sholokhov\Exchange\Factory\Exchange\FieldPreparationPipelineFactory;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Services\FieldPreparationPipeline;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Процесс выполнения обмена данных
 */
class Processor implements ProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Задача обработки значения
     *
     * @var FieldPreparationPipeline
     */
    private readonly FieldPreparationPipeline $pipeline;

    /**
     * Объект обмена данных
     *
     * @var ExchangeInterface
     */
    private readonly ExchangeInterface $engine;

    /**
     * Диспетчер событий обмена
     *
     * @var ExchangeEventDispatcher|null
     */
    private readonly ?ExchangeEventDispatcher $dispatcher;

    public function __construct(ExchangeInterface $engine)
    {
        $this->engine = $engine;
        $this->pipeline = FieldPreparationPipelineFactory::create($engine);
        $this->dispatcher = $engine instanceof EventDispatcherInterface ? new ExchangeEventDispatcher($engine) : null;
    }

    /**
     * Запустить процесс обмена данных
     *
     * @param iterable $source
     * @param ExchangeResultInterface $result
     * @return void
     */
    public function run(iterable $source, ExchangeResultInterface $result): void
    {
        foreach ($source as $item) {
            try {
                if ($this->isInvalidItem($item, $result)) {
                    continue;
                }

                $processResult = $this->processItem($item);

                if (!$processResult->isSuccess()) {
                    $result->addErrors($processResult->getErrors());
                }

                if ($data = $processResult->getData()) {
                    $result->getData()?->add($data);
                }
            } catch (Throwable $throwable) {
                $this->logger?->critical($throwable->getMessage());
                $result->addError(new Error($throwable->getMessage()));
            }
        }
    }

    /**
     * Процесс обмена элемента
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Throwable
     */
    protected function processItem(array $item): DataResultInterface
    {
        $prepared = $this->pipeline->prepare($item, $this->engine->getMap());

        $prepared = $this->dispatcher?->beforeImportItem($prepared);

        if ($this->engine->exists($prepared)) {
            $prepared = $this->dispatcher?->beforeUpdate($prepared);
            $result = $this->engine->update($prepared);
            $this->dispatcher?->afterUpdate($prepared, $result);
        } else {
            $prepared = $this->dispatcher?->beforeAdd($prepared);
            $result = $this->engine->add($prepared);
            $this->dispatcher?->afterAdd($prepared, $result);
        }

        $this->dispatcher?->afterImport($prepared, $result);

        return $result;
    }

    /**
     * Валидация данных обмена
     *
     * @param mixed $item
     * @param ExchangeResultInterface $result
     * @return bool
     */
    private function isInvalidItem(mixed $item, ExchangeResultInterface $result): bool
    {
        if (!is_array($item)) {
            $this->logger?->warning('Invalid source item');
            $result->addError(new Error('Invalid source item format'));

            return true;
        }

        return false;
    }
}