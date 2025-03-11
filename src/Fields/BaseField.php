<?php

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Repository\Repository;
use Sholokhov\Exchange\Repository\Types\Memory;

/**
 * Описание структуры и логики работы со свойством
 */
class BaseField implements Field
{
    /**
     * Конфигурация свойства
     *
     * @var Repository
     */
    private readonly Repository $container;

    /**
     * Является идентификационным полем
     *
     * @return bool
     */
    public function isKeyField(): bool
    {
        return $this->getContainer()->getField('key_field', false);
    }

    /**
     * Установить флаг идентификационного поля
     *
     * @param bool $value
     * @return $this
     */
    public function setKeyField(bool $value = true): self
    {
        $this->getContainer()->setField('key_field', $value);
        return $this;
    }

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
     * @return Exchange|null
     */
    public function getTarget(): ?Exchange
    {
        return $this->getContainer()->getField('target');
    }

    /**
     * Установка цели значения свойства
     *
     * @param Exchange $target
     * @return static
     */
    public function setTarget(Exchange $target): self
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
     * @return Field|null
     */
    public function getChildren(): ?Field
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
     * @param Field $children
     * @return $this
     */
    public function setChildren(Field $children): self
    {
        $this->getContainer()->setField('children', $children);
        return $this;
    }

    /**
     * Получение хранилище данных
     *
     * @return Repository
     */
    final protected function getContainer(): Repository
    {
        return $this->container ??= new Memory();
    }
}