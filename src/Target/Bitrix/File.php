<?php

namespace Sholokhov\Exchange\Target\Bitrix;

use CFile;

use Iterator;

use Sholokhov\Exchange\Application;
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\Type\DataResult;

use Psr\Log\LoggerAwareTrait;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Сохранение файла
 */
class File extends Application
{
    use LoggerAwareTrait;

    /**
     * Выполнить обмен данными
     *
     * @param Iterator $source
     * @return Result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function execute(Iterator $source): Result
    {
        $result = new DataResult();
        $values = [];

        foreach ($source as $path) {
            if (is_string($path) && strlen($path)) {
                $values[] = $this->getOptions()->get('use_cache') ? $this->saveByCache($path) : $this->save($path);
            }
        }

        $result->setData($values);

        return $result;
    }

    /**
     * Сохранить изображение с использованием кэша обмена
     *
     * @param string $path
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function saveByCache(string $path): int
    {
        if ($this->cache->has($path)) {
            return $this->cache->get($path);
        } else {
            $id = $this->save($path);
            $this->cache->setField($path, $id);
            return $id;
        }
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

        $file['MODULE_ID'] = $this->getOptions()->get('MODULE_ID');

        return (int)CFile::SaveFile($file, $file['MODULE_ID']);
    }

    /**
     * Обработка конфигураций обмена
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        if (empty($options['MODULE_ID']) || !is_string($options['MODULE_ID'])) {
            $options['MODULE_ID'] = 'iblock';
        }

        if (!isset($options['USE_CACHE']) || !is_bool($options['USE_CACHE'])) {
            $options['USE_CACHE'] = true;
        }

        return parent::normalizeOptions($options);
    }
}