<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Exception;
use InvalidArgumentException;

use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Fields\FieldInterface;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Импорт значения в список
 */
abstract class AbstractEnumeration extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     */
    public function __construct(protected readonly string $primary = 'VALUE')
    {
        $this->checkPrimary();
    }

    /**
     * Получение импорта значения
     *
     * @param FieldInterface $field
     * @return ExchangeInterface
     */
    abstract protected function getTarget(FieldInterface $field): ExchangeInterface;

    /**
     * Преобразование значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return int
     * @throws Exception
     */
    protected function logic(mixed $value, FieldInterface $field): int
    {
        $exchange = $this->getTarget($field);
        $exchange->setMap([
            (new Field)
                ->setPath(0)
                ->setCode($this->primary)
                ->setPrimary(),
        ]);

        if ($this->logger) {
            $exchange->setLogger($this->logger);
        }

        $result = $exchange->execute([[$value]]);

        if (!$result->isSuccess()) {
            throw new Exception(implode(PHP_EOL, $result->getErrorMessages()));
        }

        $data = $result->getData();

        return is_array($data) ? (int)reset($data) : 0;
    }

    /**
     * Проверка валидности первичного ключа
     *
     * @return void
     */
    private function checkPrimary(): void
    {
        if ($this->primary <> 'VALUE' && $this->primary <> 'ID' && $this->primary <> 'XML_ID') {
            throw new InvalidArgumentException('Primary key are not allowed');
        }
    }
}