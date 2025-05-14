<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;


use Sholokhov\BitrixExchange\Prepares\UserField\UFTrait;

use Sholokhov\BitrixExchange\Fields\Field;
use Sholokhov\BitrixExchange\ExchangeInterface;

/**
 * Преобразует значение имеющего связь к иной сущности
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractHandbookImport extends AbstractImport
{
    use UFTrait;

    /**
     * @param string $entityID Сущность для которой производится преобразование
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $entityID, string $primary = 'XML_ID')
    {
        $this->entityId = $entityID;
        parent::__construct($primary);
    }

    /**
     * Конфигурация импорта файла
     *
     * @param ExchangeInterface $target Импорт, который необходимо сконфигурировать
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