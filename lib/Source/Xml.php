<?php

namespace Sholokhov\BitrixExchange\Source;

use Iterator;
use CIBlockXMLFile;
use EmptyIterator;
use ArrayIterator;

use Sholokhov\BitrixExchange\ORM\Factory;
use Sholokhov\BitrixExchange\ORM\AbstractXmlDynamic;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\SystemException;

/**
 * Источник данных xml файла.
 * Весь файл хранится в таблице, что обеспечивает возможность чтение файла любого размера,
 * но присутствуют издержки в дополнительной нагрузке на сервер и время общения с БД.
 *
 * Рекомендуется использовать, если объем XML файла большой и мы готовы подождать
 *
 * @package Source
 * @version 1.0.0
 */
class Xml extends AbstractXml
{
    /**
     * @var AbstractXmlDynamic|null
     */
    private ?string $dataManager = null;
    private int $rootTagDepth = 0;

    public function __construct(string $path)
    {
        parent::__construct($path);
        Loader::includeModule('iblock');
    }

    public function __destruct()
    {
        $entity = $this->dataManager::getEntity();
        $entity->getConnection()->dropTable($entity->getDBTableName());
    }

    /**
     * Получение xml элемента по карте вложенности
     *
     * @return array
     */
    public function current(): array
    {
        $value = $this->getIterator()->current();
        return $this->dataManager::getElement($value["LEFT_MARGIN"], $value["RIGHT_MARGIN"]);
    }

    /**
     * Уровень вложенности родительского тега
     *
     * Если изменение происходит после формирования указателя({@see self::fetch()}), то он сбрасывается
     *
     * @param int $depth
     * @return $this
     */
    public function setRootTagDepth(int $depth): self
    {
        $this->rootTagDepth = $depth;
        return $this;
    }

    /**
     * Чтение и парсинг xml файла
     *
     * @param mixed $resource
     * @return Iterator
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SqlQueryException
     * @throws SystemException
     */
    protected function parsing(mixed $resource): Iterator
    {
        if (!$resource) {
            return new EmptyIterator();
        }

        $entity = $this->makeEntity();
        $this->dataManager = $entity->getDataClass();
        $entity->getConnection()->truncateTable($entity->getDBTableName());

        $ns = [];
        $facade = new CIBlockXMLFile($entity->getDBTableName());

        $facade->ReadXMLToDatabase($resource, $ns, 0);
        $elements = $this->dataManager::getElementsByName($this->rootTag, $this->rootTagDepth);

        return new ArrayIterator($elements);
    }

    /**
     * Создание сущности хранения результата парсинга
     *
     * @return Entity
     * @throws ArgumentException
     * @throws SystemException
     * @throws SqlQueryException
     */
    private function makeEntity(): Entity
    {
        return (new Factory)
            ->setParameters(['parent' => AbstractXmlDynamic::class])
            ->make();
    }
}