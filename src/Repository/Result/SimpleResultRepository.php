<?php

namespace Sholokhov\BitrixExchange\Repository\Result;

use Bitrix\Main\Diag\Debug;
use Stringable;

/**
 * Производит хранение результатов обмена в памяти.
 *
 * Данный способ хранения подходит для маленьких обменов.
 * Если данный способ хранения будет использоваться в импорте с большим массивом данных,
 * то для этого лучше выбрать {@see UidRepository}
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SimpleResultRepository implements ResultRepositoryInterface
{
    /**
     * Элементы с которыми производилось взаимодействие обменом
     *
     * @var array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    private array $items = [];

    /**
     * Добавить новое значение в хранилище
     *
     * @param string|Stringable $value
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function add(string|Stringable $value): void
    {
        $this->items[] = (string)$value;
    }

    /**
     * Получение всех значений
     *
     * @return array
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function get(): array
    {
        return $this->items;
    }
}