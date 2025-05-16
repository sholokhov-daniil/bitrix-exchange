<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;

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
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iblockId, string $primary = 'XML_ID')
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
                ->setPath(0)
                ->setCode($this->primary)
                ->setPrimary(),
        ]);

        if ($this->logger) {
            $target->setLogger($this->logger);
        }
    }
}