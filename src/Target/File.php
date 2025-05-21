<?php

namespace Sholokhov\BitrixExchange\Target;

use CFile;
use Exception;

use Sholokhov\BitrixExchange\Exchange;
use Sholokhov\BitrixExchange\Fields\FieldInterface;
use Sholokhov\BitrixExchange\Messages\Type\DataResult;
use Sholokhov\BitrixExchange\Messages\DataResultInterface;

use Sholokhov\BitrixExchange\Messages\Type\Error;
use Bitrix\Main\FileTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;

/**
 * Импорт файла
 *
 * @package Target
 * @version 1.0.0
 */
class File extends Exchange
{
    /**
     * Обработка конфигураций обмена
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        if (empty($options['module_id']) || !is_string($options['module_id'])) {
            $options['module_id'] = 'iblock';
        }

        return parent::normalizeOptions($options);
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
    protected function exists(array $item): bool
    {
        $keyField = $this->getPrimaryField();
        $externalID = $this->getExternalId((string)$item[$keyField->getIn()]);

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
     * @throws Exception
     */
    protected function add(array $item): DataResultInterface
    {
        $result = new DataResult;
        $path = $item[$this->getPrimaryField()->getIn()];
        $file = CFile::MakeFileArray($path);

        if (!$file) {
            $this->logger?->error('File receipt error: ' . $path);
            return $result->addError(new Error('Ошибка чтение файла: ' . $path));
        }

        $file['external_id'] = $this->getExternalId($path);
        $file['MODULE_ID'] = $this->getOptions()->get('MODULE_ID');

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
     * @throws Exception
     * @todo Доработать
     */
    protected function update(array $item): DataResultInterface
    {
        $keyField = $this->getPrimaryField();
        $externalID = $this->getExternalId((string)$item[$keyField->getIn()]);

        if (!$this->cache->has($externalID)) {
            $this->add($item);
        }

        return (new DataResult)->setData((int)$this->cache->get($externalID));
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
     * Проверка, что свойство является множественным
     *
     * @param FieldInterface $field
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function isMultipleField(FieldInterface $field): bool
    {
        return false;
    }
}