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
class TreeBaseField extends BaseField
{
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
     * @param Field $children
     * @return $this
     */
    public function setChildren(Field $children): self
    {
        $this->getContainer()->setField('children', $children);
        return $this;
    }
}