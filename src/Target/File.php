<?php

namespace Sholokhov\Exchange\Target;

use CFile;
use Iterator;

use Sholokhov\Exchange\Application;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\FileTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

use Psr\Log\LoggerAwareTrait;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Импорт файла
 */
class File extends Application
{
    use LoggerAwareTrait;

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
     * Выполнить обмен данными
     *
     * @param Iterator $source
     * @return Result
     * @throws ArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function execute(Iterator $source): Result
    {
        $result = new DataResult();
        $values = [];

        foreach ($source as $path) {
            if (is_string($path) && strlen($path)) {
                $id = $this->getFile($path) ?: $this->save($path);
                if ($id) {
                    $values[] = $id;
                }
            }
        }

        $result->setData($values);

        return $result;
    }

    /**
     * Получение существующего файла
     *
     * @param string $path
     * @return int
     * @throws ArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function getFile(string $path): int
    {
        $fileId = 0;
        $externalID = $this->getExternalId($path);
        if ($this->cache->has($externalID)) {
            $fileId = $this->cache->get($externalID);
        } elseif ($file = FileTable::getRow(['filter' => ['EXTERNAL_ID' => $externalID], 'select' => ['ID']])) {
            $fileId = (int)$file['ID'];
            $this->cache->setField($externalID, $fileId);
        }

        return $fileId;
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
     * Сохранить без кэша
     *
     * @param string $path
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function save(string $path): int
    {
        $file = CFile::MakeFileArray($path);

        if (!$file) {
            return 0;
        }

        $file['external_id'] = $this->getExternalId($path);
        $file['MODULE_ID'] = $this->getOptions()->get('MODULE_ID');

        if ($fileId = (int)CFile::SaveFile($file, $file['MODULE_ID'])) {
            $this->cache->setField($path, $fileId);
        }

        return $fileId;
    }
}