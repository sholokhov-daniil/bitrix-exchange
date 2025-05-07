<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Exception;

use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Импорт файла
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractFile extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     * Преобразование значения
     *
     * @param mixed $value
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
        $data = is_array($data) ? reset($data) : null;

        return $this->normalize($data);
    }

    /**
     * Конфигурация импорта файла
     *
     * @param ExchangeInterface $target
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private function configurationTarget(ExchangeInterface $target): void
    {
        $target->setMap([
            (new Field)
                ->setPath(0)
                ->setCode('PATH')
                ->setPrimary()
        ]);

        if ($this->logger) {
            $target->setLogger($this->logger);
        }
    }
}