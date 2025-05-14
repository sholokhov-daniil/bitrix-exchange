<?php

namespace Sholokhov\BitrixExchange\Fields\Catalog;

use Sholokhov\BitrixExchange\Fields\Field;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Currency\CurrencyManager;

/**
 * Свойство отвечающее за валюту
 *
 * @version 1.0.0
 */
class PriceField extends Field implements PriceFieldInterface
{
    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        if (!Loader::includeModule('currency')) {
            throw new LoaderException('Module "currency" not installed');
        }
    }

    /**
     * Основная валюта
     * Если значение на задано, то вернет на основе настроек сайта
     *
     * @return string
     */
    public function getCurrency(): string
    {
        $container = $this->getContainer();
        return $container->has('currency') ? $container->get('currency') : $this->setCurrency()->getCurrency();
    }

    /**
     * Установка основной валюты
     *
     * Если значение не задано, то валюта берется из настроек сайта
     *
     * @param string|null $currency Валюта в которой указана цена
     * @return $this
     */
    public function setCurrency(string $currency = null): static
    {
        $currency ??= (string)CurrencyManager::getBaseCurrency();
        $this->getContainer()->set('currency', $currency);

        return $this;
    }
}