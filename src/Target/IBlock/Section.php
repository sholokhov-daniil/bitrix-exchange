<?php

namespace Sholokhov\BitrixExchange\Target\IBlock;

use CUtil;
use Exception;
use CIBlockSection;

use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Helper\Helper;
use Sholokhov\BitrixExchange\Helper\Site;

use Sholokhov\BitrixExchange\Preparation\UserField as Prepare;
use Sholokhov\BitrixExchange\Messages\DataResultInterface;
use Sholokhov\BitrixExchange\Messages\ResultInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;

use Sholokhov\BitrixExchange\Messages\Type\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\ArgumentException;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Sholokhov\BitrixExchange\Messages\Type\Result;
use Sholokhov\BitrixExchange\Repository\Fields\UFRepository;
use Sholokhov\BitrixExchange\Target\Attributes\BootstrapConfiguration;

/**
 * Импорт разделов информационного блока
 *
 * @package Target
 * @version 1.0.0
 */
class Section extends IBlock
{
    public const BEFORE_DEACTIVATE = 'onBeforeIBlockSectionsDeactivate';
    public const BEFORE_UPDATE_EVENT = 'onBeforeIBlockSectionUpdate';
    public const AFTER_UPDATE_EVENT = 'onAfterIBlockSectionUpdate';
    public const BEFORE_ADD_EVENT = 'onBeforeIBlockSectionAdd';
    public const AFTER_ADD_EVENT = 'onAfterIBlockSectionAdd';

    /**
     * @return string
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function getUfEntityID(): string
    {
        $options = $this->getOptions();
        if (!$options->has('uf_entity_id')) {
            $options->set('uf_entity_id', 'IBLOCK_' . $this->getIBlockID() . '_SECTION');
        }

        return $options->get('uf_entity_id');
    }

    /**
     * Проверка наличия раздела
     *
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();

        if (!isset($item[$keyField->getIn()])) {
            return false;
        }

        if ($this->cache->has($item[$keyField->getIn()])) {
            return true;
        }

        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            $keyField->getIn() => $item[$keyField->getIn()],
        ];

        if ($section = CIBlockSection::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->set($item[$keyField->getIn()], (int)$section['ID']);
            return true;
        }

        return false;
    }

    /**
     * Добавление раздела
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     */
    protected function add(array $item): DataResultInterface
    {
        $result = new DataResult;
        $section = new CIBlockSection;
        $fields = $this->prepareItem($item);

        $resultBeforeAdd = $this->beforeAdd($fields);
        if (!$resultBeforeAdd->isSuccess()) {
            return $result->addErrors($resultBeforeAdd->getErrors());
        }

        if ($id = $section->Add($fields)) {
            $result->setData((int)$id);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $id));

            if ($keyField = $this->getPrimaryField()) {
                $this->cache->set($item[$keyField->getIn()], (int)$id);
            }
        } else {
            $result->addError(new Error('Error while adding IBLOCK section: ' . strip_tags($section->getLastError()), 500, $fields));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['ID' => $id, 'FIELDS' => $fields]))->send();

        return $result;
    }

    /**
     * Обновление раздела
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     */
    protected function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $keyField = $this->getPrimaryField();

        $section = new CIBlockSection;
        $sectionId = $this->cache->get($item[$keyField->getIn()]);

        if (!$sectionId) {
            return $this->add($item);
        }

        $result->setData((int)$sectionId);

        $preparedItem = $this->prepareItem($item);
        if (!isset($preparedItem['ACTIVE'])) {
            $preparedItem['ACTIVE'] = 'Y';
        }

        $resultBeforeUpdate = $this->beforeUpdate($preparedItem);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        if (!$section->Update($sectionId, $preparedItem)) {
            return $result->addError(new Error('Error while updating IBLOCK section: ' . $section->getLastError(), 500, ['ID' => $sectionId, 'FIELDS' => $preparedItem]));
        }

        $this->logger?->debug('Updated properties IBLOCK section: ' . $sectionId);
        $this->cleanCache();

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, $preparedItem))->send();

        return $result;
    }

    /**
     * Преобразование данных, которые поддерживаются разделами
     *
     * @param array $item
     * @return array
     * @throws Exception
     *
     * @version 1.0.0
     */
    protected function prepareItem(array $item): array
    {
        $result = [];
        $translitOptions = $this->getIBlockInfo()->get('FIELDS')['CODE']['DEFAULT_VALUE'] ?? [];

        foreach ($this->getMap() as $field) {
            $value = $item[$field->getIn()] ?? '';

            if ($field->getIn() === 'CODE' && $translitOptions) {
                $value = CUtil::translit($value, Site::getLanguage(), $translitOptions);
            }

            $result[$field->getIn()] = $value;
        }

        if (!isset($result['NAME'])) {
            $result['NAME'] = $item[$this->getPrimaryField()?->getIn()] ?? '';
        }

        if (!isset($result['CODE'])) {
            $result['CODE'] = CUtil::translit($result['NAME'], Site::getLanguage(), $translitOptions);
        }

        $result['IBLOCK_ID'] = $this->getIBlockID();

        return $result;
    }

    /**
     * Деактивация разделов, которые не пришли в импорте
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    protected function deactivate(): void
    {
        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            '<TIMESTAMP_X' => DateTime::createFromTimestamp($this->dateUp),
            'ACTIVE' => 'Y',
        ];
        $select = ['ID'];

        $parameters = compact('filter', 'select');

        (new Event(Helper::getModuleID(), self::BEFORE_DEACTIVATE, ['PARAMETERS' => &$parameters]))->send();

        $iterator = SectionTable::getList($parameters);
        while ($section = $iterator->fetch()) {
            SectionTable::update($section['ID'], ['ACTIVE' => 'N']);
        }
    }

    /**
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function isMultipleField(FieldInterface $field): bool
    {
        $repository = $this->getFieldRepository();
        return $repository->has($field->getIn()) && $repository->get($field->getIn())['MULTIPLE'] === 'Y';
    }

    /**
     * Получить хранилище данных свойств
     *
     * @return UFRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected function getFieldRepository(): UFRepository
    {
        return $this->repository->get('uf_repository');
    }

    /**
     * Конфигурация механизмов обмена
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[BootstrapConfiguration]
    private function configuration(): void
    {
        $this->repository->set('uf_repository', new UFRepository(['entity_id' => 'IBLOCK_' . $this->getIBlockID() . '_SECTION']));
    }

    /**
     * Событие перед обновлением раздела
     *
     * @param array $item
     * @return ResultInterface
     */
    private function beforeUpdate(array &$item): ResultInterface
    {
        $result = new Result;

        $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['FIELDS' => &$item]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                continue;
            }

            $parameters = $eventResult->getParameters();
            if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                $result->addError(new Error('Error while updating IBLOCK section: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }

    /**
     * Событие перед созданием раздела
     *
     * @param array $item
     * @return ResultInterface
     */
    private function beforeAdd(array $item): ResultInterface
    {
        $result = new Result;

        $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['ITEM' => &$item]);
        $event->send();

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() === EventResult::SUCCESS) {
                continue;
            }

            $parameters = $eventResult->getParameters();
            if (empty($parameters['ERRORS']) || !is_array($parameters['ERRORS'])) {
                $result->addError(new Error('Error while adding IBLOCK section: stopped', 300, $item));
            } else {
                foreach ($parameters['ERRORS'] as $error) {
                    $result->addError(new Error($error, 300, $item));
                }
            }
        }

        return $result;
    }

    /**
     * Инициализация преобразователей импортированных значений
     *
     * @return void
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    #[BootstrapConfiguration]
    private function bootstrapPrepares(): void
    {
        $entityId = $this->getUfEntityID();

        $this->addPrepared(new Prepare\File($entityId))
            ->addPrepared(new Prepare\Date($entityId))
            ->addPrepared(new Prepare\DateTime($entityId))
            ->addPrepared(new Prepare\Boolean($entityId))
            ->addPrepared(new Prepare\IBlockElement($entityId))
            ->addPrepared(new Prepare\IBlockSection($entityId))
            ->addPrepared(new Prepare\Enumeration($entityId));

        // Адрес
        // Видео
        // Деньги
        // Опрос
        // Привязка к элементам справочника
        // Содержимое ссылки
        // Ссылка
        // Целое число
        // Число
        // Шаблон
    }
}