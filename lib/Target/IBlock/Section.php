<?php

namespace Sholokhov\Exchange\Target\IBlock;

use Bitrix\Main\LoaderException;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use CUtil;
use Exception;
use CIBlockSection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sholokhov\Exchange\Builder\SectionUtsBuilder;
use Sholokhov\Exchange\Dispatcher\ExternalEventDispatcher;
use Sholokhov\Exchange\Dispatcher\ExternalEventTypes;
use Sholokhov\Exchange\Events\ExchangeEvent;
use Sholokhov\Exchange\Exception\ExchangeException;
use Sholokhov\Exchange\Exception\Target\ExchangeItemStoppedException;
use Sholokhov\Exchange\ExchangeMapTrait;
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
use Sholokhov\Exchange\Target\Attributes\Configuration;

/**
 * Импорт разделов информационного блока
 *
 * @package Target
 */
class Section extends IBlock
{
    use ExchangeMapTrait;

    public const BEFORE_DEACTIVATE = 'onBeforeIBlockSectionsDeactivate';
    public const BEFORE_UPDATE_EVENT = 'onBeforeIBlockSectionUpdate';
    public const AFTER_UPDATE_EVENT = 'onAfterIBlockSectionUpdate';
    public const BEFORE_ADD_EVENT = 'onBeforeIBlockSectionAdd';
    public const AFTER_ADD_EVENT = 'onAfterIBlockSectionAdd';

    private ?ExternalEventDispatcher $eventDispatcher = null;

    /**
     * @return string
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
    public function add(array $item): DataResultInterface
    {
        $result = new DataResult;
        $section = new CIBlockSection;
        $fields = $this->prepareItem($item);

        $resultBeforeAdd = $this->eventDispatcher->beforeAdd($fields);
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

        $this->eventDispatcher->afterAdd($fields, $result);

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

        $resultBeforeUpdate = $this->eventDispatcher->beforeUpdate($sectionId, $preparedItem);
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

        $this->eventDispatcher->afterUpdate($sectionId, $preparedItem, $result);

        return $result;
    }

    /**
     * Деактивация разделов, которые не пришли в импорте
     *
     * @return void
     * @throws ArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function deactivate(): void
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

        $this->eventDispatcher->beforeDeactivate(['query' => &$query]);

        $iterator = $query->exec();
        while ($section = $iterator->fetch()) {
            SectionTable::update($section['ID'], ['ACTIVE' => 'N']);
        }
    }

    /**
     * Преобразование данных, которые поддерживаются разделами
     *
     * @param array $item
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws LoaderException
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
     * Свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     */
    public function isMultipleField(FieldInterface $field): bool
    {
        $repository = $this->getFieldRepository();
        return $repository->has($field->getTo()) && $repository->get($field->getTo())['MULTIPLE'] === 'Y';
    }

    /**
     * Конфигурация обмена данными по умолчанию
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[\Sholokhov\Exchange\Target\Attributes\Event(ExchangeEvent::BeforeRun)]
    private function configuration(): void
    {
        $this->repository->set('uf_repository', new UFRepository(['entity_id' => 'IBLOCK_' . $this->getIBlockID() . '_SECTION']));

        $eventTypes = new ExternalEventTypes;
        $eventTypes->beforeDeactivate = 'onBeforeIBlockSectionsDeactivate';
        $eventTypes->beforeUpdate = 'onBeforeIBlockSectionUpdate';
        $eventTypes->afterUpdate = 'onAfterIBlockSectionUpdate';
        $eventTypes->beforeAdd = 'onBeforeIBlockSectionAdd';
        $eventTypes->afterAdd = 'onAfterIBlockSectionAdd';
        $this->eventDispatcher = new ExternalEventDispatcher($eventTypes, $this);

        $entityId = $this->getUfEntityID();
        $this->processor
            ->addPrepared(new Prepare\File($entityId))
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