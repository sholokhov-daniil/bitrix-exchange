<?php

namespace Sholokhov\Exchange;

use Exception;
use ReflectionException;

use Sholokhov\Exchange\Helper\Entity;
use Sholokhov\Exchange\Repository\Repository;
use Sholokhov\Exchange\Target\Attributes\CacheContainer;
use Sholokhov\Exchange\Target\Attributes\OptionsContainer;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[OptionsContainer]
#[CacheContainer]
abstract class Application implements Exchange
{
    /**
     * Конфигурация обмена
     *
     * @var Repository
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
     * @param array $options Конфигурация объекта
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function __construct(array $options = [])
    {
        $this->options = $this->makeOptionRepository($options);
        $this->cache = $this->makeCacheRepository();
    }

    /**
     * Конфигурация текущего обмена
     *
     * @return void
     */
    protected function configure(): void
    {
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
     * @throws ReflectionException
     * @throws Exception
     */
    private function makeOptionRepository(array $options = []): Repository
    {
        /** @var OptionsContainer $attribute */
        $attribute = Entity::getAttribute($this, OptionsContainer::class) ?: Entity::getAttribute(self::class, OptionsContainer::class);

        $entity = $attribute->getEntity();

        if (!is_subclass_of($entity, Repository::class)) {
            throw new Exception('The exchange configuration repository is not a subclass of ' . Repository::class);
        }

        return new $entity($options);
    }

    /**
     * Инициализация хранилища кэша
     *
     * @return Repository
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function makeCacheRepository(): Repository
    {
        /** @var CacheContainer $attribute */
        $attribute = Entity::getAttribute($this, CacheContainer::class) ?: Entity::getAttribute(self::class, CacheContainer::class);
        $entity = $attribute->getEntity();

        if (!is_subclass_of($entity, Repository::class)) {
            throw new Exception('The exchange cache repository is not a subclass of ' . Repository::class);
        }

        $options = $this->options->get('cache') ?: [];

        return new $entity($options);
    }
}