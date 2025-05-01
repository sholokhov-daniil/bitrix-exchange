<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Prepares\PrepareInterface;
use Sholokhov\BitrixExchange\Target\IBlock\Element as Exchange;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Базовый класс отвечающий за преобразование значения хранящий привязки к элементу ИБ
 */
abstract class AbstractElement implements PrepareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Выполнить преобразование
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     */
    public function prepare(mixed $value, FieldInterface $field): mixed
    {
        $exchange = $this->makeExchange();
        $source = $this->getSource($value, $field);
        $result = $exchange->execute($source);

        if (!$result->isSuccess()) {
            $this?->logger->error('Error converting a binding type field to an information block element: ' . implode('.', $result->getErrorMessages()));
        }

        return $exchange->execute($source)->getData();
    }

    /**
     * Формирование источника данных
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return iterable
     */
    private function getSource(mixed $value, FieldInterface $field): iterable
    {
        $value = FieldHelper::normalizeValue($value, $field);

        if ($field->isMultiple()) {
            $source = array_map(fn($id) => ['ID' => $id], $value);
        } else {
            $source = [
                ['ID' => $value]
            ];
        }

        return $source;
    }

    /**
     * Создание механизма обмена
     *
     * @return ExchangeInterface
     */
    private function makeExchange(): ExchangeInterface
    {
        $exchange = new Exchange;
        $exchange->setMap([
            (new Field)
                ->setPath('ID')
                ->setCode('XML_ID')
                ->setPrimary()
        ]);

        if ($this->logger) {
            $exchange->setLogger($this->logger);
        }

        return $exchange;
    }
}