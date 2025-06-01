<?php

namespace Sholokhov\BitrixExchange;

use Exception;
use ReflectionException;

use Sholokhov\BitrixExchange\Bootstrap\Loader;
use Sholokhov\BitrixExchange\Helper\Entity;
use Sholokhov\BitrixExchange\Repository\RepositoryInterface;
use Sholokhov\BitrixExchange\Target\Attributes\CacheContainer;
use Sholokhov\BitrixExchange\Target\Attributes\OptionsContainer;

/**
 * @since 1.0.0
 * @version 1.0.0
 */
#[OptionsContainer]
#[CacheContainer]
abstract class Application implements ExchangeInterface
{
    /**
     * Конфигурация обмена
     *
     * @var RepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private readonly RepositoryInterface $options;

    /**
     * Кэш данных, которые принимали участие в обмене
     *
     * @todo Потом поменять подход
     * @var RepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected readonly RepositoryInterface $cache;

    /**
     * @param array $options Конфигурация объекта
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(array $options = [])
    {
        $this->options = $this->makeOptionRepository($options);
        $this->cache = $this->makeCacheRepository();

        $this->bootstrap();
    }

    /**
     * Предназначен для преобразования(обработки) конфигураций перед сохранением
     *
     * @param array $options
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function normalizeOptions(array $options): array
    {
        return $options;
    }

    /**
     * Конфигурация обмена
     *
     * @return RepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getOptions(): RepositoryInterface
    {
        return $this->options;
    }

    /**
     * Вызов методов и функций отвечающих за загрузку конфигураций
     *
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function bootstrap(): void
    {
        $loader = new Loader($this);
        $loader->bootstrap();
    }

    /**
     * Инициализация хранилища настроек обмена
     *
     * @param array $options
     * @return RepositoryInterface
     * @throws ReflectionException
     * @throws Exception
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function makeOptionRepository(array $options = []): RepositoryInterface
    {
        /** @var OptionsContainer $attribute */
        $attribute = Entity::getAttributeChain($this, OptionsContainer::class);
        $entity = $attribute->getEntity();

        if (!is_subclass_of($entity, RepositoryInterface::class)) {
            throw new Exception('The exchange configuration repository is not a subclass of ' . RepositoryInterface::class);
        }

        return new $entity($options);
    }

    /**
     * Инициализация хранилища кэша
     *
     * @return RepositoryInterface
     * @throws ReflectionException
     * @throws Exception
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function makeCacheRepository(): RepositoryInterface
    {
        /** @var CacheContainer $attribute */
        $attribute = Entity::getAttribute($this, CacheContainer::class) ?: Entity::getAttribute(self::class, CacheContainer::class);
        $entity = $attribute->getEntity();

        if (!is_subclass_of($entity, RepositoryInterface::class)) {
            throw new Exception('The exchange cache repository is not a subclass of ' . RepositoryInterface::class);
        }

        $options = $this->options->get('cache') ?: [];

        return new $entity($options);
    }
}