<?php

namespace Sholokhov\Exchange\Target\Catalog;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CCurrency;
use Exception;
use ReflectionException;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;

class Currency extends AbstractExchange
{
    /**
     * Обработка параметров импорта
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        if (!isset($options['deactivate']) || !is_bool($options['deactivate'])) {
            $options['deactivate'] = false;
        }

        return parent::normalizeOptions($options);
    }

    /**
     * Проверка возможности выполнения обмена
     *
     * @return ResultInterface
     * @throws LoaderException
     * @throws ReflectionException
     */
    protected function check(): ResultInterface
    {
        $result = parent::check();

        if (!Loader::includeModule('currency')) {
            $result->addError(new Error('Module "currency" not installed'));
        }

        return $result;
    }

    /**
     * Проверка наличия валюты
     *
     * @param array $item
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getKeyField();

        if ($this->cache->has($item[$keyField->getCode()])) {
            return true;
        }

        $currency = CurrencyTable::getRow([
            'filter' => [
                $keyField->getCode() => $item[$keyField->getCode()],
            ],
            'select' => ['ID'],
            'cache' => ['ttl' => 36000]
        ]);

        if ($currency) {
            $this->cache->set($item[$keyField->getCode()], (int)$currency['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание валюты
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;
        $preparedItem = $this->prepareItem($item);

        // TODO: before event

        if ($id = CCurrency::Add($preparedItem)) {
            $result->setData((int)$id);
            $this->logger?->debug("A currency with an identifier was created: $id");
            $this->cache->set($item[$this->getKeyField()->getCode()], (int)$id);
        } else {
            $result->addError(new Error('Failed to create currency', 500, $preparedItem));
        }

        // TODO: Implement add() method.

        return $result;
    }

    protected function update(array $item): ResultInterface
    {
        // TODO: Implement update() method.
    }

    /**
     * Преобразование импортируемого значения
     *
     * @param array $item
     * @return array
     */
    private function prepareItem(array $item): array
    {
        if (!isset($item['LANG']['LID'])) {
            $item['LANG']['LID'] = $this->getSiteID();
        }

        if (!isset($item['LANG']['FORMAT_STRING'])) {
            $item['LANG']['FORMAT_STRING'] = $this->getOptions()->get('format_string');
        }

        if (!isset($item['LANG']['FULL_NAME'])) {
            $item['LANG']['FULL_NAME'] = $this->getOptions()->get('full_name');
        }

        return $item;
    }
}