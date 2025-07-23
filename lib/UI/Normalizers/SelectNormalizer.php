<?php

namespace Sholokhov\Exchange\UI\Normalizers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Нормализация настроек списка, для использования в UI
 *
 * @since 1.2.0
 * @version 1.2.0
 */
class SelectNormalizer implements NormalizerInterface
{
    /**
     * Нормализация значения
     *
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return float|int|bool|\ArrayObject|array|string|null
     * @since 1.2.0
     * @version 1.2.0
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        if (!empty($data['options']['enums']) && is_array($data['options']['enums'])) {
            $data['options']['enums'] = array_map(function(array $enum) {
                return [
                    'value' => $enum['value'],
                    'name' => $enum['title'],
                ];
            }, $data['options']['enums']);
        }

        return $data;
    }

    /**
     * Проверка возможности нормализации
     *
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return bool
     * @since 1.2.0
     * @version 1.2.0
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_array($data) && $format === 'array';
    }

    /**
     * Получение поддерживаемых типов
     *
     * @param string|null $format
     * @return array
     * @since 1.2.0
     * @version 1.2.0
     */
    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}