<?php

namespace Sholokhov\Exchange\Container;

class Options extends Container
{
    /**
     * Карта обмена
     *
     * @return array
     * @author Daniil S.
     */
    public function getMap(): array
    {
        return (array)$this->get('map');
    }
}