<?php

namespace Sholokhov\Exchange\Messages\Type;

class AddResult extends DataResult
{
    /**
     * Указание ID созданного элемента
     *
     * @param int $id
     * @return $this
     */
    public function setID(int $id): self
    {
        $this->data['ID'] = $id;
        return $this;
    }

    /**
     * Получение ID созданного элемента
     *
     * @return int
     */
    public function getID(): int
    {
        return (int)$this->getData()['ID'];
    }
}