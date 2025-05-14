<?php

namespace Sholokhov\BitrixExchange\Target\IBlock;

use Exception;

use Psr\Log\LoggerAwareTrait;
use Sholokhov\BitrixExchange\Application;
use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;

/**
 * Импортирование элемента информационного блока
 *
 * Импорт не поддерживает обновление и деактивацию элементов.
 * Создан для настройки связи с элементом ИБ при преобразовании значения свойства
 *
 * @version 1.0.0
 * @since 1.0.0
 */
class LinkElement implements ExchangeInterface
{
    use LoggerAwareTrait;

//    /**
//     * Обновление элемента
//     *
//     * @param array $item
//     * @return ResultInterface
//     * @throws Exception
//     *
//     * @version 1.0.0
//     * @since 1.0.0
//     */
//    protected function update(array $item): ResultInterface
//    {
//        $result = new DataResult;
//        $keyField = $this->getPrimaryField();
//
//        $itemID = $this->cache->get($item[$keyField->getCode()]);
//
//        if (!$itemID) {
//            return $this->add($item);
//        }
//
//        return $result->setData((int)$itemID);
//    }
//
//    /**
//     * Деактивация элементов, которые не пришли в импорте
//     *
//     * @return void
//     *
//     * @version 1.0.0
//     * @since 1.0.0
//     */
//    protected function deactivate(): void
//    {
//        return;
//    }
}