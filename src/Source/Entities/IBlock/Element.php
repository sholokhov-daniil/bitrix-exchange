<?php

namespace Sholokhov\Exchange\Source\Entities\IBlock;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Iterator;
use ArrayIterator;
use CIBlockElement;

use Sholokhov\Exchange\Source\IterableTrait;

/**
 * Источник данных основан на элементах информационного блока
 *
 * @author Daniil S.
 */
class Element implements Iterator
{
    use IterableTrait;

    public function __construct(protected readonly array $options)
    {
        if (empty($this->options['FILTER']) || !is_array($this->options['FILTER'])) {
            throw new \InvalidArgumentException("Option 'FILTER' must be an array");
        }

        if (!Loader::includeModule('iblock')) {
            throw new LoaderException('IBLOCK module is not installed.');
        }
    }

    /**
     * Загрузка элементов ИБ
     *
     * @return Iterator
     * @author Daniil S.
     */
    protected function load(): Iterator
    {
        $iterator = new ArrayIterator();

        $result = CIBlockElement::GetList(
            $this->options['ORDER'] ?? ['SORT' => 'ASC'],
            $this->options['FILTER'],
            $this->options['GROUP_BY'] ?? false,
            $this->options['NAV'] ?? false,
            $this->options['SELECT'] ?? []
        );

        while ($facade = $result->GetNextElement()) {
            $item = $facade->GetFields();
            $item['PROPERTIES'] = [];

            if (is_iterable($this->options['PROPERTIES'])) {
                foreach ($this->options['PROPERTIES'] as $code) {
                    $item['PROPERTIES'][$code] = $facade->GetProperty($code);
                }
            }

            $iterator->append($item);
        }

        return $iterator;
    }
}