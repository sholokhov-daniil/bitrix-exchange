<?php

namespace Sholokhov\Exchange\UI\DTO\Select;

use Sholokhov\Exchange\UI\DTO;

use Symfony\Component\Serializer\Attribute\Ignore;

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
    #[Ignore]
    public function getApi(): DTO\ApiInterface
    {
        $api = $this->getOption('api');
        if (!$api) {
            $api = new DTO\Api;
            $this->setApi($api);
        }

        return $api;
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
        return $this->addOption('api', $api);
    }

    /**
     * Получение списка значений
     *
     * @return EnumValueInterface[]
     * @since 1.2.0
     * @version 1.2.0
     */
    #[Ignore]
    public function getEnums(): array
    {
        return (array)$this->getOption('enums', []);
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

        return $this->addOption('enums', $iterator);
    }
}