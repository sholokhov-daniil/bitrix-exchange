<?php

namespace Sholokhov\Exchange\Target\IBlock\Catalog;

use CPrice;
use Exception;

use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\Fields\Catalog\CatalogFieldInterface;
use Sholokhov\Exchange\Fields\Catalog\PriceFieldInterface;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;
use Sholokhov\Exchange\Target\Attributes\Event;
use Sholokhov\Exchange\Target\IBlock\Element;

use Bitrix\Main\Loader;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ObjectPropertyException;

/**
 * Импорт товаров в простой каталог
 * @todo Еще в разработке. Использовать можно, но много, что еще требуется реализовать
 *
 * @package Target
 * @version 1.0.0
 * @since 1.0.0
 */
class SimpleProduct extends Element
{
    /**
     * Установка цены продукта после импорта элемента
     *
     * @param array $item
     * @param ResultInterface $result
     * @return void
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[Event(ExchangeEvent::AfterImportItem)]
    private function setPrice(array $item, ResultInterface $result): void
    {
        if (!$result->isSuccess()) {
            return;
        }

        if ($id = $result->getData()) {
            $map = $this->getMap();

            foreach ($map as $field) {
                if ($field instanceof PriceFieldInterface) {
                    $price = (float)($item[$field->getTo()] ?? 0);
                    CPrice::SetBasePrice($id, $price, $field->getCurrency());
                    break;
                }
            }
        }
    }

    /**
     * Установка общего остатка продукта
     *
     * @param array $item
     * @param DataResultInterface $result
     * @return void
     * @throws ObjectPropertyException
     * @throws SystemException
     * @todo Установку остатков реализовать как отдельный обмен
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[Event(ExchangeEvent::AfterImportItem)]
    private function setQuantity(array $item, DataResultInterface $result): void
    {
        if (!$result->isSuccess()) {
            return;
        }

        if ($id = $result->getData()) {
            $map = $this->getMap();

            foreach ($map as $field) {
                if ($field instanceof CatalogFieldInterface && $field->isQuantity()) {
                    if (ProductTable::getCount(['ID' => $id])) {
                        ProductTable::update($id, ['QUANTITY' => $item[$field->getTo()]]);
                    } else {
                        ProductTable::add(['ID' => $id, 'QUANTITY' => $item[$field->getTo()]]);
                    }
                    break;
                }
            }
        }
    }

    /**
     * Загрузка сопутствующих модулей
     *
     * @return void
     * @throws Exception
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapModules(): void
    {
        if (!Loader::includeModule("catalog")) {
            throw new Exception('Not installed module "catalog"');
        }

        if (!Loader::includeModule("currency")) {
            throw new Exception('Not installed module "currency"');
        }
    }
}
