<?php

namespace Sholokhov\Exchange\Target;

use CFile;
use Exception;

use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Messages\Type\DataResult;

use Bitrix\Main\Error;
use Bitrix\Main\FileTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;

/**
 * Импорт файла
 */
class File extends AbstractExchange
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
        $keyField = $this->getKeyField();
        $externalID = $this->getExternalId((string)$item[$keyField->getCode()]);

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
     * @return ResultInterface
     * @throws Exception
     */
    protected function add(array $item): ResultInterface
    {
        $result = new DataResult;
        $path = $item[$this->getKeyField()->getCode()];
        $file = CFile::MakeFileArray($path);

        if (!$file) {
            $this->logger?->error('File receipt error: ' . $path);
            return $result->addError(new Error('Ошибка создания получения файла'));
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
     * @todo Доработать
     * @param array $item
     * @return ResultInterface
     */
    protected function update(array $item): ResultInterface
    {
        return new DataResult;
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
}