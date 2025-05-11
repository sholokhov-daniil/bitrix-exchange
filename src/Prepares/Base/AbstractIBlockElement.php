<?php

namespace Sholokhov\BitrixExchange\Prepares\Base;

use Exception;

use Sholokhov\BitrixExchange\Prepares\IBlock\PropertyTrait;
use Sholokhov\Exchange\Fields\Field;
use Sholokhov\Exchange\ExchangeInterface;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Prepares\AbstractPrepare;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Преобразует значение имеющего связь к элементу информационного блока
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AbstractIBlockElement extends AbstractPrepare implements LoggerAwareInterface
{
    use LoggerAwareTrait, PropertyTrait;

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
     * @param string $primary Ключ по которому будет производиться проверка уникальности
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function __construct(int $iblockId, string $primary = 'XML_ID')
    {
        $this->iblockId = $iblockId;
        $this->primary = $primary;
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

        return is_array($data) ? reset($data) : null;
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
                ->setCode($this->primary)
                ->setPrimary(),
        ]);

        if ($this->logger) {
            $target->setLogger($this->logger);
        }
    }
}