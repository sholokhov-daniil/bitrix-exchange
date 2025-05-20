<?php

namespace Sholokhov\BitrixExchange\Fields;

use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Repository\Types\Memory;
use Sholokhov\BitrixExchange\Repository\RepositoryInterface;

/**
 * Описание структуры и логики работы со свойством
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Field
 */
class Field implements FieldInterface
{
    /**
     * Конфигурация свойства
     *
     * @var RepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private readonly RepositoryInterface $container;

    /**
     * Является идентификационным полем
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isPrimary(): bool
    {
        return $this->getContainer()->get('key_field', false);
    }

    /**
     * При отсутствии сущности попытается создать
     *
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function isCreatedLink(): bool
    {
        return $this->getContainer()->get('is_created_link', true);
    }

    /**
     * Установить флаг идентификационного поля
     *
     * @param bool $value
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setPrimary(bool $value = true): self
    {
        $this->getContainer()->set('key_field', $value);
        return $this;
    }

    /**
     * При отсутствии сущности попытается создать
     *
     * @param bool $value
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setCreatedLink(bool $value = false): self
    {
        $this->getContainer()->set('is_created_link', $value);
        return $this;
    }

    /**
     * Получение пути размещения значения свойства
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getPath(): string
    {
        return $this->getContainer()->get('path', '');
    }

    /**
     * Установка пути размещения значения свойства
     *
     * @param string $path
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setPath(string $path): self
    {
        $this->getContainer()->set('path', $path);
        return $this;
    }

    /**
     * Код свойства в которое будет записано значение
     *
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getCode(): string
    {
        return $this->getContainer()->get('code', '');
    }

    /**
     * Установка кода в который необходимо записать значение
     *
     * @param string $code
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setCode(string $code): self
    {
        $this->getContainer()->set('code', $code);
        return $this;
    }

    /**
     * Получение цели значения свойства
     *
     * @return ExchangeInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getTarget(): ?ExchangeInterface
    {
        return $this->getContainer()->get('target');
    }

    /**
     * Установка цели значения свойства
     *
     * @param ExchangeInterface $target
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setTarget(ExchangeInterface $target): self
    {
        $this->getContainer()->set('target', $target);
        return $this;
    }

    /**
     * Получение дочернего элемента
     *
     * @return FieldInterface|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getChildren(): ?FieldInterface
    {
        return $this->getContainer()->get('children', null);
    }

    /**
     * Получение нормализаторов значения свйоства
     *
     * @return callable[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getNormalizers(): array
    {
        return $this->getContainer()->get('normalizers', []);
    }

    /**
     * Указание нормализаторов свойства
     *
     * @param array $normalizers
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setNormalizers(array $normalizers): self
    {
        $this->getContainer()->set('normalizers', []);
        array_walk($normalizers, [$this, 'addNormalizer']);

        return $this;
    }

    /**
     * Добавление нормализатора данных
     *
     * @param callable $callback
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function addNormalizer(callable $callback): self
    {
        $data = $this->getNormalizers();
        $data[] = $callback;
        $this->getContainer()->set('normalizers', $data);
        return $this;
    }

    /**
     * Установка дочернего элемента
     *
     * Описание свойства, которое имеет итерационные значения на своем пути
     * Подходит, если необходимо получить ID изображения
     * <item>
     *     <name>NAME</name>
     *     <images>
     *          <image id="35" />
     *          <image id="35" />
     *      </images>
     * </item>
     *
     * @param FieldInterface $children
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setChildren(FieldInterface $children): self
    {
        $this->getContainer()->set('children', $children);
        return $this;
    }

    /**
     * Получение хранилище данных
     *
     * @final
     * @return RepositoryInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function getContainer(): RepositoryInterface
    {
        return $this->container ??= new Memory();
    }
}