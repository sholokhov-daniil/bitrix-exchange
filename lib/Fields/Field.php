<?php

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Preparation\PreparationInterface;
use Sholokhov\Exchange\Repository\Types\Memory;
use Sholokhov\Exchange\Repository\RepositoryInterface;

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
    public function getFrom(): string
    {
        return $this->getContainer()->get('out', '');
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
    public function setFrom(string $path): self
    {
        $this->getContainer()->set('out', $path);
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
    public function getTo(): string
    {
        return $this->getContainer()->get('in', '');
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
    public function setTo(string $code): self
    {
        $this->getContainer()->set('in', $code);
        return $this;
    }

    /**
     * Получение цели значения свойства
     *
     * @return callable|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getPreparation(): ?callable
    {
        return $this->getContainer()->get('preparation');
    }

    /**
     * Установка цели значения свойства
     *
     * @param callable $target
     * @return static
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setPreparation(callable $target): self
    {
        $this->getContainer()->set('preparation', $target);
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
     * @return callable|null
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getNormalizer(): ?callable
    {
        return $this->getContainer()->get('normalizer');
    }

    /**
     * Указание нормализаторов свойства
     *
     * @param callable $normalizer
     * @return $this
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function setNormalizer(callable $normalizer): self
    {
        $this->getContainer()->set('normalizer', $normalizer);

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