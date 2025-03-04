<?php

namespace Sholokhov\Exchange;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Error;

use Bitrix\Main\Result;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Source\Item;
use Sholokhov\Exchange\Source\Items;
use Sholokhov\Exchange\Source\SourceAwareInterface;
use Sholokhov\Exchange\Source\SourceAwareTrait;
use Sholokhov\Exchange\Source\SourceInterface;
use Sholokhov\Exchange\Target\TargetInterface;

class Exchange implements SourceAwareInterface
{
    use SourceAwareTrait, LoggerAwareTrait;

    private ?TargetInterface $target = null;

    private Result $result;

    private array $map = [];

    final public function run(): void
    {
//        try {
            if (!$this->check()) {
                return;
            }

            while ($item = $this->source->fetch()) {
                if (!is_array($item)) {
                    $this->logger?->warning('The source value is not an array: ' . json_encode($item));
                    continue;
                }

                $this->action($item);
            }
//        } catch (\Throwable $throwable) {
//            $this->result->addError(new Error($throwable->getMessage(), $throwable->getCode()));
//            $this->logger?->critical(LoggerHelper::exceptionToString($throwable));
//        }

        // Удаление элементов, которые не обновились
    }

    public function getSiteID(): string
    {
        // TODO: Implement getSiteID() method.
    }

    /**
     * @return FieldInterface[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Указание источника данных участвующих в обмене данных
     *
     * @param SourceInterface $source
     * @return $this
     */
    public function setSource(SourceInterface $source): self
    {
        $this->source = $source;
        return $this;
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

    public function setTarget(TargetInterface $target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Результат работы
     *
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result ??= new Result();
    }

    private function action(array $item): void
    {
        // Событие перед импортом
        // Нормализовать данные в соответствии с картой

        $item = $this->normalize($item);

        Debug::dump($item);
    }

    private function prepare(mixed $value, FieldInterface $field): mixed
    {
        $target = $field->getTarget();
        if (!$target) {
            return $value;
        }

        $source = $field->isMultiple() ? new Items($value) : new Item($value);

        if ($this->logger && $target instanceof LoggerAwareInterface) {
            $target->setLogger($this->logger);
        }

        return $target->setSource($source)
            ->execute();
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

    private function check(): bool
    {
        if (!$this->source) {
            $this->getResult()->addError(new Error('Source is not set', 404));
            // TODO: указать доп информацию
            $this->logger?->critical('Source is not set');
            return false;
        }

        if (!$this->target) {
            $this->getResult()->addError(new Error('Target is not set', 404));
            // TODO: указать доп информацию
            $this->logger?->critical('Target is not set');
            return false;
        }

        return true;
    }
}