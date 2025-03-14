<?php

namespace Sholokhov\Exchange\Target\IBlock\Catalog;

use ReflectionException;

use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Target\IBlock\Element;

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Импорт товаров в простой каталог
 * @todo Еще в разработке
 */
class SimpleProduct extends Element
{
    /**
     * Проверка возможности произвести импорт
     *
     * @return Result
     * @throws LoaderException
     * @throws ReflectionException
     */
    protected function check(): Result
    {
        throw new \Exception('Not work');
        $result = parent::check();

        if ($result->isSuccess() && !Loader::includeModule("catalog")) {
            $result->addError(new Error('Not installed module "catalog"'));
        }

        return $result;
    }

    /**
     * Конфигурация обмена
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->event->subscribeBeforeActionItem([$this, 'setPrice']);
        parent::configure();
    }

    private function setPrice(array $item, Result $result): void
    {
        if (!$result->isSuccess()) {
            return;
        }

        $keyField = $this->getKeyField();
        $itemId = $this->cache->get($item[$keyField->getCode()]);
        if (!$itemId) {
            return;
        }


    }
}