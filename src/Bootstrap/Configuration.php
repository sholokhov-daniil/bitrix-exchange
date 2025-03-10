<?php

namespace Sholokhov\Exchange\Bootstrap;

use Sholokhov\Exchange\Repository\Repository;
use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Repository\Types\Configuration as Registry;

/**
 * Конфигурация контейнера
 */
class Configuration
{
    private array $configurations = ['exchange'];

    /**
     * Конфигурация хранилища
     *
     * @param Repository $repository
     * @return void
     */
    public function bootstrap(Repository $repository): void
    {
        $configuration = new Memory;
        array_walk($this->configurations, fn($name) => $configuration->setField($name, new Registry($name)));
        $repository->setField('configuration', $configuration);
    }
}