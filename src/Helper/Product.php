<?php

namespace Sholokhov\BitrixExchange\Helper;

use Bitrix\Catalog\Model\Price;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;

class Product
{

    /**
     * Установка цен продукта
     *
     * @param int $productId
     * @param array $prices
     * @return void
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function setPrice(int $productId, array $prices)
    {
        foreach ($prices as $price) {
            $price = Price::getRow([
                'select' => [
                    'ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID',
                    'QUANTITY_FROM', 'QUANTITY_TO'
                ],
                'filter' => [
                    '=PRODUCT_ID' => $productId, '=CATALOG_GROUP_ID' => $price['GROUP_ID']
                ],
            ]);

            $dataPrice = [
                'PRODUCT_ID' => $productId,
                'CATALOG_GROUP_ID' => $price['GROUP_ID'],
                'CURRENCY' => $price['CURRENCY'],
                'PRICE' => $price['VALUE']
            ];

            if ($price) {
                Price::update($price['ID'], $price);
            } else {
                Price::add($dataPrice);
            }
        }
    }
}