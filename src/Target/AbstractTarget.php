<?php

namespace Sholokhov\Exchange\Target;

use Sholokhov\Exchange\Container\Container;
use Sholokhov\Exchange\Container\Repository;

abstract class AbstractTarget implements TargetInterface
{
    /**
     * Кэш данных, которые принимали участие в обмене
     *
     * @todo Потом поменять подход
     * @var Repository|Container
     */
    protected readonly Repository $cache;

    /**
     * Конфигурация обмена
     *
     * @var Repository|mixed
     */
    private readonly Repository $options;

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
     * @return Repository
     */
    protected function getOptions(): Repository
    {
        return $this->options;
    }
}