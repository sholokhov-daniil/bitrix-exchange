<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Sholokhov\BitrixExchange\Fields\Field;
use Sholokhov\BitrixExchange\ExchangeInterface;

/**
 * Импорт значения в список
 *
 * @version 1.0.0
 * @since 1.0.0
 */
abstract class AbstractEnumeration extends AbstractImport
{
    /**
     * Поддерживаемые ключи связи
     *
     * @var array|string[]
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected array $supportedPrimaries = ['VALUE', 'ID', 'XML_ID'];

    /**
     * Связующий ключ по умолчанию
     *
     * @var string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected string $defaultPrimary = 'VALUE';

    /**
     * Нормализация результата импорта значения
     *
     * @param mixed $value
     * @return int
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function normalize(mixed $value): int
    {
        return max((int)$value, 0);
    }

    /**
     * Конфигурация импорта файла
     *
     * @param ExchangeInterface $target
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function configurationTarget(ExchangeInterface $target): void
    {
        $target->setMap([
            (new Field)
                ->setPath(0)
                ->setCode($this->primary)
                ->setPrimary(),
        ]);

        if ($this->logger) {
            $target->setLogger($this->logger);
        }
    }
}