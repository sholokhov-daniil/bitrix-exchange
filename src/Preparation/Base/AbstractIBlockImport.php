<?php

namespace Sholokhov\BitrixExchange\Preparation\Base;

use Sholokhov\BitrixExchange\Preparation\IBlock\PropertyTrait;

use Sholokhov\BitrixExchange\Fields\Field;
use Sholokhov\BitrixExchange\ExchangeInterface;

/**
 * Преобразует значение имеющего связь к иной сущности
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractIBlockImport extends AbstractImport
{
    use PropertyTrait;

    /**
     * Связующий ключ по умолчанию
     *
     * @var string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected string $defaultPrimary = 'XML_ID';

    /**
     * @param string|null $primary Ключ по которому будет производиться проверка уникальности
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iblockId, string $primary = null)
    {
        $this->iblockId = $iblockId;
        parent::__construct($primary);
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
                ->setFrom(0)
                ->setTo($this->primary)
                ->setPrimary(),
        ]);

        if ($this->logger) {
            $target->setLogger($this->logger);
        }
    }
}