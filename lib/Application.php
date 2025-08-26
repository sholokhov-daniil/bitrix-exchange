<?php

namespace Sholokhov\Exchange;

use ReflectionException;

use Sholokhov\Exchange\Bootstrap\Loader;
use Sholokhov\Exchange\Factory\RepositoryFactory;
use Sholokhov\Exchange\Repository\RepositoryInterface;
use Sholokhov\Exchange\Target\Attributes\CacheContainer;
use Sholokhov\Exchange\Target\Attributes\OptionsContainer;

#[OptionsContainer]
#[CacheContainer]
abstract class Application implements ExchangeInterface
{
    /**
     * Конфигурация обмена
     *
     * @var RepositoryInterface
     */
    private readonly RepositoryInterface $options;

    /**
     * Кэш данных, которые принимали участие в обмене
     *
     * @todo Потом поменять подход
     * @var RepositoryInterface
     */
    protected readonly RepositoryInterface $cache;

    /**
     * @param array $options Конфигурация объекта
     * @throws ReflectionException
     */
    public function __construct(array $options = [])
    {
        $this->options = RepositoryFactory::createOptions($this, $options);
        $this->cache = RepositoryFactory::createCache($this, $this->options->get('cache'));

        $this->bootstrap();
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

    /**
     * Вызов методов и функций отвечающих за загрузку конфигураций
     *
     * @return void
     */
    private function bootstrap(): void
    {
        (new Loader($this))->bootstrap();
    }
}