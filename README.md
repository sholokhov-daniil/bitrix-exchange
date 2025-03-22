# Пользовательский обмен данных для Bitrix


Библиотека позволяет производить обмен данными между различными сущностями оптимизируя трудозатратность и обеспечивая единую структуру.

Эта библиотека рассчитана на расширение со стороны разработчика и позволяет максимальную модификацию процесса без вмешательства в исходный код или копирования существующего функционала с целью доработки под свои требования.

Проект открыт для ваших предложений улучшения совместимости существующих механизмов или разработки новых решений, которые будут крайне полезны, для других разработчиков.


## Установка
Установить последнюю версию библиотеки

````bash
composer require sholokhov/bitrix-exchange
````

## Базовое использование обмена

````php
use Sholokhov\Exchange\Fields;
use Sholokhov\Exchange\Target\IBlock\Element;

$data = [
    [
        'id' => 56,
        'name' => 'Какой-то элемент',
    ],
    [
        'id' => 15,
        'name' => 'Какой-то элемент 2',
    ]
];

$map = [
    (new Fields\Field)
        ->setPath('id')
        ->setCode('XML_ID')
        ->setKeyField(),
    (new Fields\Field)
        ->setPath('name')
        ->setCode('NAME'),
];

$exchange = new Element;
$exchange->setMap($map);
$result = $exchange->execute($data);
````

## Документация
* [Использование](https://github.com/sholokhov-daniil/bitrix-exchange/blob/master/doc/01-usage.md)
* [События](https://github.com/sholokhov-daniil/bitrix-exchange/blob/master/doc/01-events.md)

## Автор
Шолохов Даниил - <sholokhovdaniil@yandex.ru> - <https://t.me/sholokhov22>