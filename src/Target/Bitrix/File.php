<?php

namespace Sholokhov\Exchange\Target\Bitrix;

use CFile;

use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\Messages\ResultInterface;
use Sholokhov\Exchange\Target\AbstractTarget;
use Sholokhov\Exchange\Source\SourceAwareTrait;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Сохранение файла
 */
class File extends AbstractTarget implements LoggerAwareInterface
{
    use SourceAwareTrait, LoggerAwareTrait;

    /**
     * Выполнить обмен данными
     *
     * @return ResultInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function execute(): ResultInterface
    {
        $result = new Result;
        $values = [];

        while ($path = $this->source->fetch()) {
            $values[] = $this->getOptions()->get('USE_CACHE') ? $this->saveByCache($path) : $this->save($path);
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

        return (int)CFile::SaveFile($file, $this->getOptions()->get('SAVE_PATH'));
    }

    /**
     * Обработка конфигураций обмена
     *
     * @param array $options
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        if (!is_string($options['SAVE_PATH']) || empty($options['SAVE_PATH'])) {
            $options['SAVE_PATH'] = 'iblock';
        }

        if (!is_bool($options['USE_CACHE'])) {
            $options['USE_CACHE'] = true;
        }

        return parent::normalizeOptions($options);
    }
}