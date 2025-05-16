<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use ReflectionException;

use Sholokhov\BitrixExchange\Fields\Field;
use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Fields\LinkFieldInterface;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;
use Sholokhov\BitrixExchange\Repository\IBlock\ElementRepository;
use Sholokhov\BitrixExchange\Target\IBlock\Element;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Преобразователь значения в идентификатор информационного блока
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractIBlockElement extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Идентификатор информационного блока к которому привязано свойство
     *
     * @param FieldInterface $field Свойство на основе которого производится поиск
     * @return int
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function getFieldIBlockID(FieldInterface $field): int;

    /**
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(private readonly string $primary = 'XML_ID')
    {
    }

    /**
     * Логика преобразование значения в идентификатор элемента информационного блока
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return int
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function logic(mixed $value, FieldInterface $field): int
    {
        $result = 0;

        if (is_null($value) || $value === '') {
            return $result;
        }

        if ($field instanceof LinkFieldInterface && $field->isAppend()) {
            $result = $this->runExchange($value, $field);
        } else {
            $result = $this->runRepository($value, $field);
        }

        // todo: Обработка ошибок

        return $result->getData();
    }

    /**
     * Импортирование элемента
     *
     * @param mixed $value Преобразуемое значение
     * @param FieldInterface $field Свойство на основе которого будет производиться импорт элемента
     * @return ResultInterface
     * @throws ReflectionException
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function runExchange(mixed $value, FieldInterface $field): ResultInterface
    {
        $exchange = new Element([
            'iblock_id' => $this->getFieldIBlockID($field),
            'primary' => $this->primary,
        ]);

        $exchange->setMap([
            (new Field)
                ->setPath(0)
                ->setCode($this->primary)
                ->setPrimary(),
        ]);

        if ($this->logger) {
            $exchange->setLogger($this->logger);
        }

        return $exchange->execute([[$value]]);
    }

    /**
     * Поиск элемента по первичному ключу
     *
     * @param mixed $value Преобразуемое значение
     * @param FieldInterface $field Свойство на основе которого будет производиться поиск элемента
     * @return ResultInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function runRepository(mixed $value, FieldInterface $field): ResultInterface
    {
        $result = new DataResult;

        $repository = new ElementRepository([
            'iblock_id' => $this->getFieldIBlockID($field),
            'primary' => $this->primary
        ]);

        return $result->setData((int)$repository->get($value)?->GetFields()['ID'] ?? 0);
    }

    /**
     * Нормализация результата импорта значения
     *
     * @param mixed $value
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function normalize(mixed $value): int
    {
        return is_array($value) ? (int)reset($value) : 0;
    }
}