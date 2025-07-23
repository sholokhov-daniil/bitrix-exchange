<?php

namespace Sholokhov\Exchange\UI\DTO\Select;

use Sholokhov\Exchange\Repository\Types\MemoryTrait;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * Описание значения списка
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class EnumValue implements EnumValueInterface
{
    use MemoryTrait;

    /**
     * Значение элемента списка
     *
     * @return mixed
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getValue(): mixed
    {
        return $this->getRepository()->get('value');
    }

    /**
     * Указание значения списка
     *
     * @param mixed $value
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setValue(mixed $value): static
    {
        $this->getRepository()->set('value', $value);
        return $this;
    }

    /**
     * Текстовое описание значения
     *
     * @return string
     * @since 1.2.0
     * @version 1.2.0
     */
    #[SerializedName('name')]
    public function getTitle(): string
    {
        return $this->getRepository()->get('title', '');
    }

    /**
     * Указание текстового описания значения
     *
     * @param string $title
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setTitle(string $title): static
    {
        $this->getRepository()->set('title', $title);
        return $this;
    }
}