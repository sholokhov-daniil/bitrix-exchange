<?php

namespace Sholokhov\Exchange\Target\IBlock;

use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use CUtil;
use Exception;
use CIBlockSection;

use Sholokhov\Exchange\Builder\SectionUtsBuilder;
use Sholokhov\Exchange\Exception\ExchangeException;
use Sholokhov\Exchange\Exception\Target\ExchangeItemStoppedException;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Helper\Helper;
use Sholokhov\Exchange\Helper\Site;

use Sholokhov\Exchange\Preparation\UserField as Prepare;
use Sholokhov\Exchange\Messages\DataResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\Type\EventResult;

use Sholokhov\Exchange\Messages\Type\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult as BXEventResult;
use Bitrix\Main\ArgumentException;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Sholokhov\Exchange\Repository\Fields\UFRepository;
use Sholokhov\Exchange\Target\Attributes\BootstrapConfiguration;

/**
 * Импорт разделов информационного блока
 *
 * @package Target
 * @version 1.1.0
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
     * Получить хранилище данных свойств
     *
     * @return UFRepository
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    public function getFieldRepository(): UFRepository
    {
        return $this->repository->get('uf_repository');
    }

    /**
     * Проверка наличия раздела
     *
     * @param array $item
     * @return bool
     * @throws Exception
     *
     * @version 1.1.0
     */
    public function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();

        if (!isset($item[$keyField->getTo()])) {
            return false;
        }

        if ($this->cache->has($item[$keyField->getTo()])) {
            return true;
        }

        $filter = [
            'IBLOCK_ID' => $this->getIBlockID(),
            $keyField->getTo() => $item[$keyField->getTo()],
        ];

        if ($hashField = $this->getHashField()) {
            $filter[$hashField->getTo()] = $this->getHash();
        }

        if ($section = CIBlockSection::GetList([], $filter)->Fetch()) {
            // TODO: Проверить хэш импорта
            $this->cache->set($item[$keyField->getTo()], (int)$section['ID']);
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

        if ($resultBeforeAdd->isStopped()) {
            return $result;
        }

        if ($id = $section->Add($fields)) {
            $result->setData((int)$id);
            $this->logger?->debug(sprintf('An element with the identifier "%s" has been added to the %s information block', $this->getIBlockID(), $id));

            if ($keyField = $this->getPrimaryField()) {
                $this->cache->set($item[$keyField->getTo()], (int)$id);
            }
        } else {
            $result->addError(new Error('Error while adding IBLOCK section: ' . strip_tags($section->getLastError()), 500, $fields));
        }

        (new Event(Helper::getModuleID(), self::AFTER_ADD_EVENT, ['id' => $id, 'fields' => $fields, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Обновление раздела
     *
     * @param array $item
     * @return DataResultInterface
     * @throws Exception
     */
    public function update(array $item): DataResultInterface
    {
        $result = new DataResult;
        $keyField = $this->getPrimaryField();

        $section = new CIBlockSection;
        $sectionId = $this->cache->get($item[$keyField->getTo()]);

        if (!$sectionId) {
            return $this->add($item);
        }

        $result->setData((int)$sectionId);

        $preparedItem = $this->prepareItem($item);
        if (!isset($preparedItem['ACTIVE'])) {
            $preparedItem['ACTIVE'] = 'Y';
        }

        $resultBeforeUpdate = $this->beforeUpdate($sectionId, $preparedItem);
        if (!$resultBeforeUpdate->isSuccess()) {
            return $result->addErrors($resultBeforeUpdate->getErrors());
        }

        if ($resultBeforeUpdate->isStopped()) {
            return $result;
        }

        if (!$section->Update($sectionId, $preparedItem)) {
            return $result->addError(new Error('Error while updating IBLOCK section: ' . $section->getLastError(), 500, ['ID' => $sectionId, 'FIELDS' => $preparedItem]));
        }

        $this->logger?->debug('Updated properties IBLOCK section: ' . $sectionId);
        $this->cleanCache();

        (new Event(Helper::getModuleID(), self::AFTER_UPDATE_EVENT, ['fields' => $preparedItem, 'id' => $sectionId, 'result' => $result]))->send();

        return $result;
    }

    /**
     * Преобразование данных, которые поддерживаются разделами
     *
     * @param array $item
     * @return array
     * @throws Exception
     *
     * @version 1.1.0
     */
    protected function prepareItem(array $item): array
    {
        $result = [];
        $translitOptions = $this->getIBlockInfo()->get('FIELDS')['CODE']['DEFAULT_VALUE'] ?? [];

        foreach ($this->getMap() as $field) {
            $value = $item[$field->getTo()] ?? '';

            if ($field->getTo() === 'CODE' && $translitOptions) {
                $value = CUtil::translit($value, Site::getLanguage(), $translitOptions);
            }

            $result[$field->getTo()] = $value;
        }

        if (!isset($result['NAME'])) {
            $result['NAME'] = $item[$this->getPrimaryField()?->getTo()] ?? '';
        }

        if (!isset($result['CODE'])) {
            $result['CODE'] = CUtil::translit($result['NAME'], Site::getLanguage(), $translitOptions);
        }

        $result['IBLOCK_ID'] = $this->getIBlockID();

        if ($hashField = $this->getHashField()) {
            $result[$hashField->getTo()] = $this->getHash();
        }

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
     *
     * @version 1.1.0
     */
    protected function deactivate(): void
    {
        $query = SectionTable::query()
            ->where('IBLOCK_ID', $this->getIBlockID())
            ->where('TIMESTAMP_X', '<', DateTime::createFromTimestamp($this->getDateStarted()))
            ->where('ACTIVE', 'Y')
            ->addSelect('ID');

        if ($hashField = $this->getHashField()) {
            if ($this->getFieldRepository()->has($hashField->getTo())) {
                $factory = new SectionUtsBuilder($this->getIBlockID());
                $uts = $factory->make([new StringField($hashField->getTo())]);
                $query->registerRuntimeField(
                    new Reference('UF', $uts, ['=this.ID' => 'ref.VALUE_ID'], ['join_type' => 'inner'])
                );
            } else {
                $query->where($hashField->getTo(), $this->getHash());
            }
        }

        (new Event(Helper::getModuleID(), self::BEFORE_DEACTIVATE, ['query' => $query]))->send();

        $iterator = $query->exec();
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
    public function isMultipleField(FieldInterface $field): bool
    {
        $repository = $this->getFieldRepository();
        return $repository->has($field->getTo()) && $repository->get($field->getTo())['MULTIPLE'] === 'Y';
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
     * @param int $id
     * @param array $item
     * @return EventResult
     */
    private function beforeUpdate(int $id, array &$item): EventResult
    {
        $result = new EventResult;

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_UPDATE_EVENT, ['fields' => &$item, 'id' => $id, 'exchange' => $this]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BXEventResult::SUCCESS) {
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
        } catch (ExchangeException $e) {
            $this->logger?->warning(($e->getMessage() ?: 'Error while updating IBLOCK section') . ': ' . $id);
            $result->setStopped();
        }

        return $result;
    }

    /**
     * Событие перед созданием раздела
     *
     * @param array $item
     * @return EventResult
     */
    private function beforeAdd(array $item): EventResult
    {
        $result = new EventResult();

        try {
            $event = new Event(Helper::getModuleID(), self::BEFORE_ADD_EVENT, ['fields' => &$item, 'exchange' => $this]);
            $event->send();

            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() === BXEventResult::SUCCESS) {
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
        } catch (ExchangeItemStoppedException $e) {
            $this->logger?->warning(($e->getMessage() ?: 'Error while updating IBLOCK section') . ': ' . json_encode($item));
            $result->setStopped();

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