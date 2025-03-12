<?php

namespace Sholokhov\Exchange\Target\IBlock;

use CIBlock;
use CIBlockElement;

use Sholokhov\Exchange\Fields\IBlock\ElementField;
use Sholokhov\Exchange\Helper\Site;
use Sholokhov\Exchange\Messages\Errors\Error;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Импортирование элемента информационного блока
 */
class Element extends IBlock
{
    /**
     * Проверка наличия элемента
     *
     * @param array $item
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getKeyField();

        if (!$keyField || !isset($item[$keyField->getCode()])) {
            return false;
        }

        if ($this->cache->has($item[$keyField->getCode()])) {
            return true;
        }

        // TODO: Добавлять хэш импорта
        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            'ACTIVE' => 'Y',
        ];

        if ($keyField instanceof ElementField) {
            $filter['PROPERTY_' . $keyField->getCode()] = $item[$keyField->getCode()];
        } else {
            $filter[$keyField->getCode()] = $item[$keyField->getCode()];
        }

        if ($element = CIBlockElement::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->setField($item[$keyField->getCode()], (int)$element['ID']);

            return true;
        }

        return false;
    }

    /**
     * Добавление элемента в информационный блок
     *
     * @param array $item
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function add(array $item): Result
    {
        $result = new DataResult;
        $iblock = new CIBlockElement;

        $preparedItem = $this->prepareItem($item);
        $data = $preparedItem['FIELDS'];
        $data['IBLOCK_ID'] = $this->getIBlockID();
        $data['PROPERTY_VALUES'] = $preparedItem['PROPERTIES'] ?? [];

        // TODO: Событие перед добавлением

        $itemId = $iblock->Add($data);

        if ($itemId) {
            // TODO: записываем хэш
            $result->setData((int)$itemId);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $itemId));

            if ($keyField = $this->getKeyField()) {
                $this->cache->setField($item[$keyField->getCode()], (int)$itemId);
            }
        } else {
            $result->addError(new Error('Error while adding IBLOCK element: ' . $iblock->getLastError(), 500, $data));
        }

        // TODO: Событие после добавления

        return $result;
    }

    /**
     * Обновление элемента
     *
     * @param array $item
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function update(array $item): Result
    {
        $result = new DataResult;

        // TODO: Событие перед обновлением

        $keyField = $this->getKeyField();

        if (!$keyField) {
            return $result->addError(new Error('Error while updating IBLOCK element: No identification field'));
        }

        $iBlock = new CIBlockElement;
        $itemID = $this->cache->get($item[$keyField->getCode()]);

        if (!$itemID) {
            return $result->addError(new Error('Error while updating IBLOCK element: No ID'));
        }

        $preparedItem = $this->prepareItem($item);
        if (!$iBlock->Update($itemID, $preparedItem['FIELDS'])) {
            return $result->addError(new Error('Error while updating IBLOCK element: ' . $iBlock->getLastError(), 500, ['ID' => $itemID, 'FIELDS' => $preparedItem['FIELDS']]));
        }

        $this->logger?->debug('Updated fields IBLOCK element: ' . $itemID);

        $iBlock::SetPropertyValuesEx($itemID, $this->getIBlockID(), $preparedItem['PROPERTIES']);
        $this->logger?->debug('Updated properties IBLOCK element: ' . $itemID);

        $this->cleanCache();

        // TODO: Событие после обновления

        return $result->setData($itemID);
    }

    /**
     * Разделение импортируемых данных на группы
     *
     * @param array{FIELDS: array, PROPERTIES: array} $item
     * @return array|array[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function prepareItem(array $item): array
    {
        $result = [
            'FIELDS' => [],
            'PROPERTIES' => []
        ];

        foreach ($this->getMap() as $field) {
            $group = 'FIELDS';
            $value = $item[$field->getCode()];

            if ($field instanceof ElementField && $field->isProperty()) {
                $group = 'PROPERTIES';
            } elseif ($field->getCode() === 'CODE') {
                $translitOptions = $this->getIBlockInfo()['FIELDS']['CODE']['DEFAULT_VALUE'] ?? [];

                if ($translitOptions) {
                    $value = \CUtil::translit($value, Site::getLanguage(), $translitOptions);
                }
            }

            $result[$group][$field->getCode()] = $value;
        }

        if (!isset($result['FIELDS']['NAME'])) {
            $result['FIELDS']['NAME'] = $item[$this->getKeyField()?->getCode()] ?? '';
        }

        return $result;
    }
}