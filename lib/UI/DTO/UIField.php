<?php

namespace Sholokhov\Exchange\UI\DTO;

use Sholokhov\Exchange\Repository\Types\MemoryTrait;

/**
 * Конфигурация отображения HTML элемента
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class UIField implements UIFieldInterface
{
    use MemoryTrait;

    /**
     * Указание механизма отображения
     *
     * @param string $view
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setView(string $view): static
    {
        $this->getRepository()->set('view', $view);
        return $this;
    }

    /**
     * Формат отображения поля
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getView(): string
    {
        return $this->getRepository()->get('view', '');
    }

    /**
     * Установка уникального наименования поля
     *
     * @param string $name
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setName(string $name): static
    {
        $this->getRepository()->set('name', $name);
        return $this;
    }

    /**
     * Уникальное код поля в рамках группы в которой выводится
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getName(): string
    {
        return $this->getRepository()->get('name', '');
    }

    /**
     * Установка наименования поля отображаемого в UI
     *
     * @param string $title
     * @return static
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTitle(string $title): static
    {
        $this->getRepository()->set('title', $title);
        return $this;
    }

    /**
     * Наименование свойства - отображается в UI
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getTitle(): string
    {
        return $this->getRepository()->get('title', '');
    }

    /**
     * Установка конфигурации механизма отрисовки
     *
     * @param array $options
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setOptions(array $options): static
    {
        $this->getRepository()->set('options', $options);
        return $this;
    }

    /**
     * Конфигурация отображения поля
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getOptions(): array
    {
        return $this->getRepository()->get('options', []);
    }
}