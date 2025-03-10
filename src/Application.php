<?php

namespace Sholokhov\Exchange;

use Sholokhov\Exchange\Repository\Container;
use Sholokhov\Exchange\Repository\Repository;
use Sholokhov\Exchange\Repository\Types\Configuration;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class Application implements Exchange
{
    /**
     * Конфигурация обмена
     *
     * @var Repository|mixed
     */
    private readonly Repository $options;

    /**
     * Кэш данных, которые принимали участие в обмене
     *
     * @todo Потом поменять подход
     * @var Repository
     */
    protected readonly Repository $cache;

    /**
     * Глобальные конфигурации обмена
     *
     * @var Configuration
     */
    protected readonly Configuration $configuration;

    /**
     * @param array $options Конфигурация объекта
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(array $options = [])
    {
        $this->configuration = Container::getInstance()->getConfiguration()->get('exchange');
        $this->options = $this->makeOptionRepository($options);
        $this->cache = $this->makeCacheRepository();
    }

    /**
     * Предназначен для преобразования(обработки) конфигураций перед сохранением
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        return $options;
    }

    /**
     * Конфигурация обмена
     *
     * @return Repository
     */
    protected function getOptions(): Repository
    {
        return $this->options;
    }

    /**
     * Инициализация хранилища настроек обмена
     *
     * @param array $options
     * @return Repository
     */
    private function makeOptionRepository(array $options = []): Repository
    {
        $entity = $this->configuration->getOptionEntity(static::class);
        $options = $this->normalizeOptions($options);

        return new $entity($options);
    }

    /**
     * Инициализация хранилища кэша
     *
     * @return Repository
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function makeCacheRepository(): Repository
    {
        $entity = $this->configuration->getCacheEntity(static::class);
        $options = $this->options->get('cache') ?: [];

        return new $entity($options);
    }
}