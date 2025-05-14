<?php

namespace Sholokhov\BitrixExchange\Prepares\UserField;

use CFile;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Преобразование значение в файл, который воспринимает пользовательское свойство (UF)
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class File extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait, UFTrait;

    /**
     * @param string $entityId ID сущности в рамках которого производится преобразование
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        $property = $this->getFieldRepository()->get($field->getCode());
        return $property && $property['USER_TYPE_ID'] === 'file';
    }

    /**
     * Логика преобразования значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return array|null
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): ?array
    {
        if (empty($value)) {
            return null;
        }

        return CFile::MakeFileArray($value) ?: null;
    }
}