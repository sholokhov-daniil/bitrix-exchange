<?php

namespace Sholokhov\Exchange\Source;

use ArrayIterator;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\SystemException;
use CFile;
use CIBlockXMLFile;
use Iterator;
use Sholokhov\Exchange\Helper\IO;
use Sholokhov\Exchange\ORM\AbstractXmlDynamic;
use Sholokhov\Exchange\ORM\Factory;

class Xml implements SourceInterface
{
    /**
     * @var AbstractXmlDynamic|null
     */
    private ?string $dataManager = null;
    private ?\Iterator $iterator = null;
    private string $rootTag = 'data';
    private int $rootTagDepth = 1;


    public function __construct(private readonly string $path)
    {
        Loader::includeModule('iblock');
    }

    public function __destruct()
    {
        // TODO: Почистить таблицу
    }

    public function fetch(): array
    {
        $this->iterator ??= $this->load();
        $element = $this->iterator->current();
        $this->iterator->next();

        if (!$element) {
            return [];
        }

        return $this->dataManager::getElement($element["LEFT_MARGIN"], $element["RIGHT_MARGIN"]);
    }

    /**
     * Указание родитеского наименования тега хранения данных
     *
     * Если изменение происходит после формирования указателя({@see self::fetch()}), то он сбрасывается
     *
     * @param string $code
     * @return $this
     */
    public function setRootTag(string $code): self
    {
        $this->rootTag = $code;

        if ($this->iterator) {
            $this->iterator = null;
        }

        return $this;
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

        if ($this->iterator) {
            $this->iterator = null;
        }

        return $this;
    }

    private function download(): void
    {
        if (file_exists($this->path)) {
            $file = $this->path;
        } else {
            $file = CFile::MakeFileArray($this->path)['tmp_name'];
        }

        $this->saveToTable($file);
    }

    private function saveToTable(string $path): void
    {
        $resource = fopen($path, 'rb');

        if (!$resource) {
            return;
        }

        $entity = $this->makeEntity();
        $this->dataManager = $entity->getDataClass();
        $entity->getConnection()->truncateTable($entity->getDBTableName());

        $ns = [];
        $facade = new CIBlockXMLFile($entity->getDBTableName());

        $facade->ReadXMLToDatabase($resource, $ns, 0);
        fclose($resource);

        Debug::dump($entity->getDBTableName());
    }

    /**
     * Получение карты содержимого файла фида
     *
     * @return Iterator
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function load(): Iterator
    {
        $this->download();

        $facade = new CIBlockXMLFile($this->dataManager::getEntity()->getDBTableName());
        $iterator = $facade->GetList();
        while($item = $iterator->Fetch()) {
            Debug::dump($item);
        }

        $elements = $this->dataManager::getElementsByName($this->rootTag, $this->rootTagDepth);

        Debug::dump($this->rootTagDepth);

        return new ArrayIterator($elements);
    }

    private function makeEntity(): Entity
    {
        return (new Factory)
            ->setParameters(['parent' => AbstractXmlDynamic::class])
            ->make();
    }
}