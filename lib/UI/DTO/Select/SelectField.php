<?php

namespace Sholokhov\Exchange\UI\DTO\Select;

use Sholokhov\Exchange\UI\DTO;

class SelectField extends DTO\UIField implements ApiSelectInterface
{
    public function __construct()
    {
        $this->setView('select');
    }

    /**
     * Получение API настроек
     *
     * @return DTO\ApiInterface
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getApi(): DTO\ApiInterface
    {
        if (!$this->getRepository()->has('api')) {
            $this->setApi(new DTO\Api);
        }

        return $this->getRepository()->get('api');
    }

    /**
     * Указание API настроек
     *
     * @param DTO\ApiInterface $api
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setApi(DTO\ApiInterface $api): static
    {
        $this->getRepository()->set('api', $api);
        return $this;
    }

    /**
     * Получение списка значений
     *
     * @return EnumValueInterface[]
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getEnums(): array
    {
        return $this->getRepository()->get('enums', []);
    }

    /**
     * Указание списка значений
     *
     * @param array $enums
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function setEnums(array $enums): static
    {
        $this->getRepository()->delete('enums');
        array_walk($enums, $this->addEnum(...));

        return $this;
    }

    /**
     * Добавление значения списка
     *
     * @param EnumValueInterface $enum
     * @return $this
     * @since 1.2.0
     * @version 1.2.0
     */
    public function addEnum(EnumValueInterface $enum): static
    {
        $iterator = $this->getEnums();
        $iterator[] = $enum;

        $this->getRepository()->set('enums', $iterator);

        return $this;
    }
}