<?php

namespace Sholokhov\Exchange\Target\Highloadblock;

use Bitrix\Main\Diag\Debug;
use Exception;
use ReflectionException;

use Sholokhov\Exchange\Exchange;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\ResultInterface;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use Bitrix\Highloadblock\HighloadBlockTable as HLT;

class Element extends Exchange
{
    protected readonly DataManager|string $entity;

    /**
     * Получение ID справочника
     *
     * @return int
     */
    public function getEntityID(): int
    {
        return $this->getOptions()->get('entity_id') ?: 0;
    }

    /**
     * Предварительная обработка конфигураций импорта
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        $options['entity_id'] = (int)$options['entity_id'];
        return parent::normalizeOptions($options);
    }

    /**
     * Конфигурация импорта
     *
     * @return void
     * @throws SystemException
     */
    protected function configure(): void
    {
        Loader::includeModule('highloadblock');
        $this->entity = HLT::compileEntity($this->getEntityID())->getDataClass();
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

        if (!Loader::includeModule('highloadblock')) {
            $result->addError(new Error('Module "highloadblock" not installed'));
        }

        if ($this->getEntityID() <= 0) {
            $result->addError(new Error('Entity ID is required'));
        }

        return $result;
    }

    /**
     * Проверка наличия элемента сущности
     *
     * @param array $item
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getKeyField();

        if ($this->cache->has($item[$keyField->getCode()])) {
            return true;
        }

        // TODO: Добавить хэш импорта
        $filter = [
            $keyField->getCode() => $item[$keyField->getCode()],
        ];

        $select = [$keyField->getCode(), 'ID'];
        $element = $this->entity::getRow(compact('filter', 'select'));

        if ($element) {
            $this->cache->set($item[$keyField->getCode()], (int)$element['ID']);
            return true;
        }

        return false;
    }

    /**
     * Создание элемента сущности
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;
        $addResult = $this->entity::add($item);

        if (!$addResult->isSuccess()) {
            $errorMessage = sprintf(
                'An error occurred when adding an element to the highloadblock "%s": %s',
                $this->getEntityID(),
                implode('. ', $addResult->getErrorMessages())
            );
            $this->logger?->critical($errorMessage);

            return $result->addError(new Error($errorMessage, 500, $item));
        }

        $result->setData($addResult->getId());
        $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the "%s" highloadblock', $addResult->getId(), $this->getEntityID()));
        $this->cache->set($item[$this->getKeyField()->getCode()], $addResult->getId());

        return $result;
    }

    /**
     * Обновление элемента сущности
     *
     * @param array $item
     * @return ResultInterface
     * @throws Exception
     */
    protected function update(array $item): ResultInterface
    {
        $result = new DataResult;
        $keyField = $this->getKeyField();

        $itemID = $this->cache->get($item[$keyField->getCode()]);

        if (!$itemID) {
            return $this->add($item);
        }

        Debug::dump($item);

        $updateResult = $this->entity::update($itemID, $item);

        if (!$updateResult->isSuccess()) {
            $errorMessage = sprintf(
                'An error occurred when updating an element with the ID "%s" to the highloadblock "%s": %s',
                $itemID,
                $this->getEntityID(),
                implode('. ', $updateResult->getErrorMessages())
            );
            $this->logger?->critical($errorMessage);

            return $result->addError(new Error($errorMessage, 500, ['ID' => $itemID, 'FIELDS' => $item]));
        }

        $this->logger?->debug(
            sprintf(
                'The element with the ID "%s" in the highloadblock "%s" has been successfully updated.',
                $itemID,
                $this->getEntityID()
            )
        );

        return $result->setData($itemID);
    }

    protected function deactivate(): void
    {
        // TODO: Добавить деактивацию, если указано свойство типа дата и время
    }
}