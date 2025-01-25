<?php

namespace Sholokhov\Exchange\Fields;

use Sholokhov\Exchange\Registry\Container;

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
     * @return FieldInterface
     */
    public function setPath(string $path): FieldInterface
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
     * @return FieldInterface
     */
    public function setCode(string $code): FieldInterface
    {
        $this->getContainer()->setField('code', $code);
        return $this;
    }

    /**
     * Получение цели значения свойства
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->getContainer()->getField('target', '');
    }

    /**
     * Установка цели значения свойства
     *
     * @param string $target
     * @return FieldInterface
     */
    public function setTarget(string $target): FieldInterface
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
     * @return FieldInterface
     */
    public function setMultiple(bool $multiple): FieldInterface
    {
        $this->getContainer()->setField('multiple', $multiple);
        return $this;
    }

    final protected function getContainer(): Container
    {
        return $this->container ??= new Container();
    }
}