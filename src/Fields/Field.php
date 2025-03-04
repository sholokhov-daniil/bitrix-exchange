<?php

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\Registry\Container;
use Sholokhov\Exchange\Target\TargetInterface;

/**
 * Описание структуры и логики работы с свойством
 */
class Field implements FieldInterface
{
    /**
     * Конфигурация свойства
     *
     * @var Container
     */
    private readonly Container $container;

    /**
     * Получение пути размещения значения свойства
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getContainer()->getField('path', '');
    }

    /**
     * Установка пути размещения значения свойства
     *
     * @param string $path
     * @return static
     */
    public function setPath(string $path): self
    {
        $this->getContainer()->setField('path', $path);
        return $this;
    }

    /**
     * Код свойства в которое будет записано значение
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->getContainer()->getField('code', '');
    }

    /**
     * Установка кода в который необходимо записать значение
     *
     * @param string $code
     * @return static
     */
    public function setCode(string $code): self
    {
        $this->getContainer()->setField('code', $code);
        return $this;
    }

    /**
     * Получение цели значения свойства
     *
     * @return TargetInterface|null
     */
    public function getTarget(): ?TargetInterface
    {
        return $this->getContainer()->getField('target');
    }

    /**
     * Установка цели значения свойства
     *
     * @param TargetInterface $target
     * @return static
     */
    public function setTarget(TargetInterface $target): self
    {
        $this->getContainer()->setField('target', $target);
        return $this;
    }

    /**
     * Значение является множественным
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->getContainer()->getField('multiple', false);
    }

    /**
     * Установка, что значение является множественным
     *
     * @param bool $multiple
     * @return static
     */
    public function setMultiple(bool $multiple = true): self
    {
        $this->getContainer()->setField('multiple', $multiple);
        return $this;
    }

    /**
     * Получение дочернего элемента
     *
     * @return FieldInterface|null
     */
    public function getChildren(): ?FieldInterface
    {
        return $this->getContainer()->getField('children', null);
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
     */
    public function setChildren(FieldInterface $children): self
    {
        $this->getContainer()->setField('children', $children);
        return $this;
    }

    final protected function getContainer(): Container
    {
        return $this->container ??= new Container();
    }
}