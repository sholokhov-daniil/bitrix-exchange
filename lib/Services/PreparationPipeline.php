<?php

namespace Sholokhov\Exchange\Services;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Normalizers\ValueNormalizer;
use Sholokhov\Exchange\Preparation\Chain;
use Sholokhov\Exchange\Preparation\PreparationInterface;

/**
 * Преобразует обработку значения на основе свойства карты обмена
 */
class PreparationPipeline
{
    private readonly Chain $engine;
    private readonly ValueNormalizer $normalizer;

    public function __construct(ValueNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
        $this->engine = new Chain;
    }

    /**
     * Преобразование значение
     *
     * @param array $item
     * @param FieldInterface[] $map
     * @return array
     */
    public function prepare(array $item, array $map): array
    {
        $result = [];

        foreach ($map as $field) {
            $value = FieldHelper::getValue($item, $field);
            $value = $this->normalizer->normalize($value, $field);

            if (is_callable($field->getPreparation())) {
                $value = ($field->getPreparation())($value, $field);
            } else {
                $value = $this->engine->prepare($value, $field);
            }

            $result[$field->getTo()] = $value;
        }

        return $result;
    }

    /**
     * Добавление преобразователя данных
     *
     * @param PreparationInterface $preparation
     * @return $this
     */
    public function add(PreparationInterface $preparation): static
    {
        $this->engine->add($preparation);
        return $this;
    }
}