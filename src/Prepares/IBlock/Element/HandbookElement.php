<?php

namespace Sholokhov\BitrixExchange\Prepares\IBlock\Element;

use ReflectionException;

use Sholokhov\BitrixExchange\Factory\Highloadblock\ProviderFactory;
use Sholokhov\BitrixExchange\Factory\Result\SimpleFactory;
use Sholokhov\BitrixExchange\Target\Highloadblock\Element;
use Sholokhov\BitrixExchange\Prepares\Base\AbstractIBlockImport;
use Sholokhov\BitrixExchange\Fields\IBlock\ElementFieldInterface;

use Sholokhov\BitrixExchange\ExchangeInterface;
use Sholokhov\BitrixExchange\Fields\FieldInterface;

use Bitrix\Main\LoaderException;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Highloadblock\HighloadBlockTable as HLT;

/**
 * Преобразует значение имеющего связь к элементу справочника
 *
 * Если элемент будет отсутствовать, то будет произведено автоматическое создание
 *
 * @package Preparation
 * @since 1.0.0
 * @version 1.0.0
 */
class HandbookElement extends AbstractIBlockImport
{
    private array $cache = [];

    /**
     * Связующий ключ по умолчанию
     *
     * @var string
     *
     * @version 1.0.0
     * @since 1.0.0
     */
    protected string $defaultPrimary = 'UF_XML_ID';

    /**
     * Инициализация импорта элементов информационного блока
     *
     * @param FieldInterface $field Свойство в которое производится преобразование
     * @return ExchangeInterface
     *
     * @throws ReflectionException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function getTarget(FieldInterface $field): ExchangeInterface
    {
        $property = $this->getPropertyRepository()->get($field->getCode());

        $result = HLT::query()
            ->where('TABLE_NAME', $property['USER_TYPE_SETTINGS']['TABLE_NAME'])
            ->setCacheTtl(36000)
            ->addSelect('ID')
            ->exec()
            ->fetch();

        return new Element([
            'result_repository' => new SimpleFactory,
            'entity_id' => $result['ID'],
        ]);
    }

    /**
     * Нормализация результата импорта значения
     *
     * @param mixed $value
     * @param FieldInterface $field
     * @return mixed
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws LoaderException
     * @since 1.0.0
     * @version 1.0.0
     */
    protected function normalize(mixed $value, FieldInterface $field): mixed
    {
        if (!is_array($value) && $this->primary <> 'ID') {
            $property = $this->getPropertyRepository()->get($field->getCode());
            $provider = $this->cache[$property['USER_TYPE_SETTINGS']['TABLE_NAME']] ??= ProviderFactory::createByTable($property['USER_TYPE_SETTINGS']['TABLE_NAME']);

            $item = $provider::getRow([
                'filter' => ['ID' => $value],
                'cache' => ['ttl' => 36000]
            ]);
            if ($item) {
                $value = $item[$this->primary];
            }
        }


        return is_array($value) ? $this->normalize(reset($value), $field) : $value;
    }

    /**
     * Проверка возможности преобразовать значение свойства
     *
     * @param mixed $value Значение, которое необходимо преобразовать
     * @param FieldInterface $field Свойство, которое преобразовывается
     * @return bool
     *
     * @since 1.0.0
     * @version 1.0.0
     */
    public function supported(mixed $value, FieldInterface $field): bool
    {
        return $field instanceof ElementFieldInterface
            && ($property = $this->getPropertyRepository()->get($field->getCode()))
            && $property['USER_TYPE'] === PropertyTable::USER_TYPE_DIRECTORY
            && $property['USER_TYPE_SETTINGS']['TABLE_NAME'];
    }
}