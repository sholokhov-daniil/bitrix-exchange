<?php

namespace Sholokhov\Exchange;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Repository\Types\Memory;

abstract class Application implements Exchange
{
    /**
     * Конфигурация обмена
     *
     * @var RepositoryInterface|mixed
     */
    private readonly RepositoryInterface $options;

    /**
     * Объект в котором будут храниться конфигурации
     *
     * @var string
     */
    protected string $optionEntity = Memory::class;

    /**
     * Объект в котором будут храниться конфигурации
     *
     * @var string
     */
    protected string $cacheEntity = Memory::class;

    /**
     * Кэш данных, которые принимали участие в обмене
     *
     * @todo Потом поменять подход
     * @var RepositoryInterface
     */
    protected readonly RepositoryInterface $cache;

    /**
     * @param array $options Конфигурация объекта
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(array $options = [])
    {
        $options = $this->normalizeOptions($options);
        $this->options = new $this->optionEntity($options);
        $this->cache = new $this->cacheEntity($this->options->get('cache') ?: []);
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
     * @return RepositoryInterface
     */
    protected function getOptions(): RepositoryInterface
    {
        return $this->options;
    }
}