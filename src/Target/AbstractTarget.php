<?php

namespace Sholokhov\Exchange\Target;

use Sholokhov\Exchange\Container\Container;
use Sholokhov\Exchange\Container\ContainerInterface;

abstract class AbstractTarget implements TargetInterface
{
    /**
     * Кэш данных, которые принимали участие в обмене
     *
     * @todo Потом поменять подход
     * @var ContainerInterface|Container
     */
    protected readonly ContainerInterface $cache;

    /**
     * Конфигурация обмена
     *
     * @var ContainerInterface|mixed
     */
    private readonly ContainerInterface $options;

    /**
     * Объект в котором будут храниться конфигурации
     *
     * @var string
     */
    protected string $optionEntity = Container::class;

    public function __construct(array $options = [])
    {
        $options = $this->normalizeOptions($options);
        $this->options = new $this->optionEntity($options);
        $this->cache = new Container;
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
     * @return ContainerInterface
     */
    protected function getOptions(): ContainerInterface
    {
        return $this->options;
    }
}