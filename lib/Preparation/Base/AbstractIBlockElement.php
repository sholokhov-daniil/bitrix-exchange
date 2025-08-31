<?php

namespace Sholokhov\Exchange\Preparation\Base;

use ReflectionException;

use Sholokhov\Exchange\Factory\Result\SimpleFactory;
use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ExchangeResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Preparation\AbstractPrepare;
use Sholokhov\Exchange\Repository\IBlock\ElementRepository;
use Sholokhov\Exchange\Repository\Map\MappingRegistry;
use Sholokhov\Exchange\Target\IBlock\Element;

use Bitrix\Main\NotImplementedException;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Преобразователь значения в идентификатор информационного блока
 *
 * @package Preparation
 */
abstract class AbstractIBlockElement extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Идентификатор информационного блока к которому привязано свойство
     *
     * @param FieldInterface $field Свойство на основе которого производится поиск
     * @return int
     */
    abstract protected function getFieldIBlockID(FieldInterface $field): int;

    /**
     * @param string $primary Ключ по которому будет производиться проверка уникальности
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotImplementedException
     * @throws ReflectionException
     */
    protected function logic(mixed $value, FieldInterface $field): int
    {
        $result = 0;

        if (is_null($value) || $value === '') {
            return $result;
        }

        if ($field->isCreatedLink()) {
            $result = $this->runExchange($value, $field)->getData()->get();
            $result = reset($result);
        } else {
            $result = $this->runRepository($value, $field)->getData();
        }

        // todo: Обработка ошибок


        return $result;
    }

    /**
     * Импортирование элемента
     *
     * @param mixed $value Преобразуемое значение
     * @param FieldInterface $field Свойство на основе которого будет производиться импорт элемента
     * @return ExchangeResultInterface
     * @throws NotImplementedException
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function runExchange(mixed $value, FieldInterface $field): ExchangeResultInterface
    {
        $exchange = new Element([
            'result_repository' => new SimpleFactory,
            'iblock_id' => $this->getFieldIBlockID($field),
            'primary' => $this->primary,
        ]);

        $mapping = (new MappingRegistry)->setFields([
            (new Field)
                ->setFrom(0)
                ->setTo($this->primary)
                ->setPrimary(),
        ]);

        $exchange->setMappingRegistry($mapping);

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
     * @return DataResultInterface
     */
    private function runRepository(mixed $value, FieldInterface $field): DataResultInterface
    {
        $result = new DataResult;

        $repository = new ElementRepository([
            'iblock_id' => $this->getFieldIBlockID($field),
            'primary' => $this->primary
        ]);

        return $result->setData((int)($repository->get($value)?->GetFields()['ID'] ?? 0));
    }

    /**
     * Нормализация результата импорта значения
     *
     * @param mixed $value
     * @return mixed
     */
    protected function normalize(mixed $value): int
    {
        return is_array($value) ? (int)reset($value) : 0;
    }
}