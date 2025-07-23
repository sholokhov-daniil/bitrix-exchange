<?php

namespace Sholokhov\Exchange\UI\DTO;

use Sholokhov\Exchange\Repository\Types\MemoryTrait;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

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
        $metadataFactory = new ClassMetadataFactory(new AttributeLoader);
        $nameConverter = new MetadataAwareNameConverter($metadataFactory);
        $normalizer = new ObjectNormalizer($metadataFactory, $nameConverter, null, new ReflectionExtractor);

        $serializer = new Serializer([$normalizer]);
        return (array)$serializer->normalize($this);
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
    #[Ignore]
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->getOptions()[$key] ?? $default;
    }
}