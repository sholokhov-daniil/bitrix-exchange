<?php

namespace Sholokhov\Exchange\UI\DTO;

use Sholokhov\Exchange\Repository\Types\MemoryTrait;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
     * Преобразование настроек в массив воспиримаемый UI
     *
     * @return array
     * @throws ExceptionInterface
     * @since 1.2.0
     * @version 1.2.0
     */
    public function toArray(): array
    {
        $serializer = new Serializer([new ObjectNormalizer]);
        $data = (array)$serializer->normalize($this);

        $normalizer = $this->createNormalizer();
        if ($normalizer && $normalizer->supportsNormalization($data, 'array')) {
            $data = (array)$normalizer->normalize($data, 'array');
        }

        return $data;
    }

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
     * Указание дополнительных настроек
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
     * Получение дополнительных настроек
     *
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getOptions(): array
    {
        return $this->getRepository()->get('options', []);
    }

    /**
     * Добавление дополнительной настройки
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function addOption(string $key, mixed $value): static
    {
        $options = $this->getOptions();
        $options[$key] = $value;

        return $this->setOptions($options);
    }

    /**
     * Получение дополнительной настройки
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->getOptions()[$key] ?? $default;
    }

    /**
     * Создание нормализатора данных, который используется в преобразование объекта
     *
     * @return NormalizerInterface|null
     * @since 1.2.0
     * @version 1.2.0
     */
    protected function createNormalizer(): ?NormalizerInterface
    {
        return null;
    }
}