<?php

namespace Sholokhov\Exchange;

use ArrayIterator;

use Bitrix\Main\Diag\Debug;
use Iterator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Repository\RepositoryInterface;

abstract class AbstractExchange extends Application
{
    use LoggerAwareTrait;

    private Result $result;

    private array $map = [];

    abstract protected function add(array $item): Result;
    abstract protected function update(array $item): Result;
    abstract protected function exists(array $item): bool;

    final public function execute(Iterator $source): Result
    {
        $result = new Messages\Type\DataResult;

//        try {
            foreach ($source as $item) {
                if (!is_array($item)) {
                    $this->logger?->warning('The source value is not an array: ' . json_encode($item));
                    continue;
                }

                $result = $this->action($item);
            }
//        } catch (\Throwable $throwable) {
//            $this->result->addError(new Error($throwable->getMessage(), $throwable->getCode()));
//            $this->logger?->critical(LoggerHelper::exceptionToString($throwable));
//        }

        // Удаление элементов, которые не обновились

        return $result;
    }

    /**
     * Получение карты обмена
     *
     * @return Field[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Указание карты данных обмена
     *
     * @param array $map
     * @return $this
     */
    public function setMap(array $map): self
    {
        // TODO: Добавить валидацию
        $this->map = $map;
        return $this;
    }


    private function action(array $item): Result
    {
        // Событие перед импортом

        $item = $this->normalize($item);

        if ($this->exists($item)) {
            $result = $this->update($item);
        } else {
            $result = $this->add($item);
        }

        // Событие по окончанию

        return $result;
    }

    private function prepare(mixed $value, Field $field): mixed
    {
        $target = $field->getTarget();
        if (!$target) {
            return $value;
        }

        $source = $field->isMultiple() ? new ArrayIterator($value) : new ArrayIterator([$value]);

        if ($this->logger && $target instanceof LoggerAwareInterface) {
            $target->setLogger($this->logger);
        }

        return $target->execute($source)
                      ->getData();
    }

    /**
     * Нормализация импортируемых данных, для восприятия системы
     *
     * @param array $item
     * @return array
     */
    private function normalize(array $item): array
    {
        $result = [];

        foreach ($this->getMap() as $field) {
            $value = FieldHelper::getValue($item, $field);

            if ($field->isMultiple() && !is_array($value)) {
                $value = $value === null ? [] : [$value];
            }

            // TODO: Вызвать пользовательские нормализаторы
            $value = $this->prepare($value, $field);

            $result[$field->getCode()] = $value;
        }

        return $result;
    }
}