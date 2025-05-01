<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Bitrix\Main\Diag\Debug;
use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Fields\FieldInterface;

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

        return $result->getData();
    }

    /**
     * Проверка валидности первичного ключа
     *
     * @return void
     */
    private function checkPrimary(): void
    {
        if ($this->primary <> 'VALUE' && $this->primary <> 'ID' && $this->primary <> 'XML_ID') {
            throw new \InvalidArgumentException('Primary key are not allowed');
        }
    }
}