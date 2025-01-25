<?php

namespace Sholokhov\Exchange\Fields;

/**
 * Описание свойства, которое имеет итерационные значения на своем пути
 *
 * Подходит, если необходимо получить ID изображения
 * <item>
 *     <name>NAME</name>
 *     <images>
 *          <image id="35" />
 *          <image id="35" />
 *      </images>
 * </item>
 */
class TreeField extends Field
{
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
     * @param FieldInterface $children
     * @return $this
     */
    public function setChildren(FieldInterface $children): self
    {
        $this->getContainer()->setField('children', $children);
        return $this;
    }
}