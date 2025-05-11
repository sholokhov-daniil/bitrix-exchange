<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Exception;
use InvalidArgumentException;

use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\FieldInterface;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Преобразует значение имеющего связь к сущности
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractImport extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Список поддерживаемых ключей связи.
     *
     * Если массив пустой, то будут поддерживаться все виды значений
     *
     * @var array|string[]
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected array $supportedPrimaries = [];

    /**
     * Ключ по которому будет производиться проверка уникальности
     *
     * @var string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected readonly string $primary;

    /**
     * Связующий ключ по умолчанию
     *
     * @var string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected string $defaultPrimary = "XML_ID";

    /**
     * Получение импорта значения
     *
     * @param FieldInterface $field
     * @return ExchangeInterface
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function getTarget(FieldInterface $field): ExchangeInterface;

    /**
     * Конфигурация импорта значения
     *
     * @param ExchangeInterface $target
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    abstract protected function configurationTarget(ExchangeInterface $target): void;

    /**
     * @param string|null $primary Ключ по которому будет производиться проверка уникальности
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(?string $primary = null)
    {
        $this->primary = $primary ?: $this->defaultPrimary;
        $this->checkPrimary();
    }

    /**
     * Логика преобразования значения
     *
     * @final
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразуется
     * @return mixed
     * @throws Exception
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    final protected function logic(mixed $value, FieldInterface $field): mixed
    {
        $target = $this->getTarget($field);
        $this->configurationTarget($target);

        $result = $target->execute([[$value]]);
        if (!$result->isSuccess()) {
            throw new Exception(implode(PHP_EOL, $result->getErrorMessages()));
        }

        $data = $result->getData();

        return $this->normalize($data);
    }

    /**
     * Преобразование результата работы цели в конечный результат.
     *
     * Метод предназначен для переопределения
     *
     * @todo Потом перейти на цепочку атрибутов и выполнять только первый от последнего ребенка
     *
     * @param mixed $value Результат цели, который необходимо нормализовать
     * @return mixed
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function normalize(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Проверка валидности первичного ключа
     *
     * @final
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    final protected function checkPrimary(): void
    {
        if (!empty($this->supportedPrimaries) && !in_array($this->primary, $this->supportedPrimaries)) {
            throw new InvalidArgumentException(sprintf('Primary key "%s" are not allowed', $this->primary));
        }
    }
}