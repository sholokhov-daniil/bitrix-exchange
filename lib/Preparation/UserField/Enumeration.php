<?php

namespace Sholokhov\Exchange\Preparation\UserField;

use ReflectionException;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Factory\Result\SimpleFactory;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Preparation\Base\AbstractEnumeration;
use Sholokhov\Exchange\Target\UserFields\Enumeration as Target;

/**
 * Производит импорт значения списка и преобразовывает значение в идентификатор значения списка
 *
 * @package Preparation
 * @version 1.0.0
 * @since 1.0.0
 */
class Enumeration extends AbstractEnumeration
{
    use UFTrait;

    /**
     * @param string $entityID Сущность для которой производится преобразование
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(string $entityID, string $primary = 'VALUE')
    {
        $this->entityId = $entityID;
        parent::__construct($primary);
    }

    /**
     * Инициализация импорта элементов списка
     *
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return ExchangeInterface
     * @throws ReflectionException
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        return new Target([
            'result_repository' => new SimpleFactory,
            'entity_id' => $this->entityId,
            'property_code' => $field->getTo(),
        ]);
    }

    /**
     * Проверка поддержки свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     * @version 1.0.0
     * @since 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return ($property = $this->getFieldRepository()->get($field->getTo())) && $property['USER_TYPE_ID'] === 'enumeration';
    }
}