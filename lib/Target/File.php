<?php

namespace Sholokhov\Exchange\Target;

use CFile;
use Exception;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sholokhov\Exchange\AbstractImport;
use Sholokhov\Exchange\ExchangeMapTrait;
use Sholokhov\Exchange\Fields\FieldInterface;
use Sholokhov\Exchange\MappingExchangeInterface;
use Sholokhov\Exchange\Messages\Type\Error;
use Sholokhov\Exchange\Messages\Type\DataResult;
use Sholokhov\Exchange\Messages\DataResultInterface;

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
class File extends AbstractImport implements MappingExchangeInterface
{
    use ExchangeMapTrait;

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
     * Конфигурация импорта
     *
     * @return void
     * @throws Exception
     */
    protected function configuration(): void
    {
        parent::configuration();
        $this->normalizeOptions();
    }

    /**
     * Получение ID значение из кэша
     *
     * @param array $item
     * @return int
     */
    protected function resolveId(array $item): int
    {
        $key = $this->getPrimaryField()->getTo();
        $externalID = $this->getExternalId((string)$item[$key]);
        return (int)$this->cache->get($externalID);
    }

    /**
     * Проверка наличия файла
     *
     * @param array $item
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function doExist(array $item): bool
    {
        $keyField = $this->getPrimaryField();
        $externalID = $this->getExternalId((string)$item[$keyField->getTo()]);

        if ($file = FileTable::getRow(['filter' => ['EXTERNAL_ID' => $externalID], 'select' => ['ID']])) {
            $fileId = (int)$file['ID'];
            $this->cache->set($externalID, $fileId);
            return true;
        }

        return false;
    }

    /**
     * Создание нового файла
     *
     * @param array $fields
     * @param array $originalFields
     * @return DataResultInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function doAdd(array $fields, array $originalFields): DataResultInterface
    {
        $result = new DataResult;
        $path = $fields[$this->getPrimaryField()->getTo()];
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
            $result->addError(new Error('Ошибка сохранения файла', 500));
        }

        return $result;
    }

    /**
     * Обновление файла
     *
     * @param int $id
     * @param array $fields
     * @param array $originalFields
     * @return DataResultInterface
     *
     * @todo Доработать
     */
    protected function doUpdate(int $id, array $fields, array $originalFields): DataResultInterface
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

    /**
     * Преобразование данных, для создания
     *
     * @param array $item
     * @return array
     */
    protected function prepareForAdd(array $item): array
    {
        return $item;
    }

    /**
     * Преобразование данных, для обновления
     *
     * @param array $item
     * @return array
     */
    protected function prepareForUpdate(array $item): array
    {
        return $item;
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