<?php

namespace Sholokhov\Exchange;

use Generator;
use Sholokhov\Exchange\Prepares\PrepareInterface;
use Sholokhov\Exchange\Target\TargetInterface;

/**
 * Производит подготовку разобранных данных, для дальнейшего импорта
 */
class Dispatcher extends AbstractApplication
{
    /**
     * @var PrepareInterface[]
     */
    private array $prepares = [];

    public function __construct(
        private readonly Generator $source,
        private readonly TargetInterface $target
    )
    {
    }

    /**
     * Указание обработчиков данных
     *
     * @param PrepareInterface[] $prepares
     * @return self
     */
    public function setPrepares(array $prepares): self
    {
        $this->prepares = [];
        array_walk($prepares, [$this, 'prepare']);
        return $this;
    }

    /**
     * Добавление обработчика данных
     *
     * @param PrepareInterface $prepare
     * @return $this
     */
    public function addPrepare(PrepareInterface $prepare): self
    {
        $this->prepares[] = $prepare;
        return $this;
    }

    protected function logic(): void
    {
        foreach ($this->source as $item) {
            $item = $this->prepare($item);

            // TODO: Событие добавления, обновления и проверки должен просать target
            if ($this->target->has($item)) {
                $targetResult = $this->target->update($item);
            } else {
                $targetResult = $this->target->add($item);
            }

            if (!$targetResult->isSuccess()) {
                $this->getResult()->addErrors($targetResult->getErrors());
            }
        }

        // TODO: Implement logic() method.
    }

    private function prepare(mixed $item): mixed
    {
        // TODO: Событие перед преобразованием

        foreach ($this->prepares as $preparation) {
            $item = $preparation->prepare($item);
        }

        // TODO: Событие после преобразования

        return $item;
    }
}