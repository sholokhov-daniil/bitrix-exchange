<?php

namespace Sholokhov\Exchange\Target;

use CFile;
use Exception;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sholokhov\Exchange\AbstractApplication;
use Sholokhov\Exchange\ExchangeMapTrait;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\DataResultInterface;

use Sholokhov\Exchange\Messages\Type\Error;
use Bitrix\Main\FileTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;

/**
 * Импорт файла
 *
 * @todo Переделать логику
 * @package Target
 */
class File extends AbstractApplication
{
    use ExchangeMapTrait;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->normalizeOptions();
    }

    /**
     * Проверка наличия файла
     *
     * @param array $item
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();
        $externalID = $this->getExternalId((string)$item[$keyField->getTo()]);

        if ($this->cache->has($externalID)) {
            return true;
        } elseif ($file = FileTable::getRow(['filter' => ['EXTERNAL_ID' => $externalID], 'select' => ['ID']])) {
            $fileId = (int)$file['ID'];
            $this->cache->set($externalID, $fileId);
            return true;
        }

        return false;
    }

    /**
     * Создание нового файла
     *
     * @param array $item
     * @return DataResultInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function add(array $item): DataResultInterface
    {
        $result = new DataResult;
        $path = $item[$this->getPrimaryField()->getTo()];
        $file = CFile::MakeFileArray($path);

        if (!$file) {
            $this->logger?->error('File receipt error: ' . $path);
            return $result->addError(new Error('Ошибка чтение файла: ' . $path));
        }

        $file['external_id'] = $this->getExternalId($path);
        $file['MODULE_ID'] = $this->getOptions()->get('module_id');

        if ($fileId = (int)CFile::SaveFile($file, $file['MODULE_ID'])) {
            $this->cache->set($path, $fileId);
            $result->setData($fileId);
        } else {
            $this->logger?->error('File receipt error: ' . $path . '. Data: ' . json_encode($file));
            $result->addError(new Error('Ошибка сохранения файла', 500, $file));
        }

        return $result;
    }

    /**
     * Обновление файла
     *
     * @param array $item
     * @return DataResultInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @todo Доработать
     */
    public function update(array $item): DataResultInterface
    {
        $keyField = $this->getPrimaryField();
        $externalID = $this->getExternalId((string)$item[$keyField->getTo()]);

        if (!$this->cache->has($externalID)) {
            $this->add($item);
        }

        return (new DataResult)->setData((int)$this->cache->get($externalID));
    }

    /**
     * Проверка, что свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     */
    public function isMultipleField(FieldInterface $field): bool
    {
        return false;
    }

    /**
     * Получение внешнего идентификатора файла
     *
     * @param string $path
     * @return string
     */
    protected function getExternalId(string $path): string
    {
        return md5($path);
    }

    /**
     * Обработка конфигураций обмена
     *
     * @return void
     */
    private function normalizeOptions(): void
    {
        if (!$this->options->get('module_id')) {
            $this->options->set('module_id', 'main');
        }
    }
}