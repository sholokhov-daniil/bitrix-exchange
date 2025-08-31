<?php

namespace Sholokhov\Exchange\Preparation;

use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\FieldHelper;
use Sholokhov\Exchange\Normalizers\ValueNormalizer;

/**
 * Преобразует обработку значения на основе свойства карты обмена
 *
 * @internal
 * @package Preparation
 */
class FieldPreparationPipeline implements FieldPreparationPipelineInterface
{
    private readonly Chain $engine;
    private readonly ?ValueNormalizer $normalizer;

    public function __construct(ValueNormalizer $normalizer = null)
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

            if ($this->normalizer) {
                $value = $this->normalizer->normalize($value, $field);
            }

            if (is_callable($field->getPreparation())) {
                $value = call_user_func($field->getPreparation(), $value, $field);
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