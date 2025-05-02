# Использование обмена
- [Установка](#установка)
- [Минимальные требования](#минимальные-требования)
- [Описание](#описание)
- [Карта обмена](#карта-импорта)
  - [Свойства](#доступные-описания-свойств)
    - [Информационный блок](#описание-свойства-информационного-блока)

# Установка
Обмен доступен на Packagist [sholokhov/bitrix-exchange](https://packagist.org/packages/sholokhov/bitrix-exchange) и поэтому устанавливается через [Composer](http://getcomposer.org/).

````bash
composer require sholokhov/bitrix-exchange
````

# Минимальные требования

* Версия php 8.2
* Реакция bitrix: старт

# Описание

Модуль обмена позволяет производить импорт\экспорт из любых в любые сущности.
За основу механизма обмена был взят модуль [sholokhov/exchange](https://github.com/sholokhov-daniil/exchange)

# Карта импорта
Более подробная документация описана в модуле [sholokhov/exchange](https://github.com/sholokhov-daniil/exchange)

## Доступные описания свойств

### Описание свойства информационного блока
Класс [IBlockElementField](https://github.com/sholokhov-daniil/bitrix-exchange/blob/v0.200/src/Fields/IBlock/IBlockElementField.php) наследник [Field](https://github.com/sholokhov-daniil/exchange/blob/master/src/Fields/Field.php) 

| Наименование | Обязательное | Тип данных параметра | Возвращаемый тип данных |           Описание           |
|:------------:|:------------:|:--------------------:|:-----------------------:|:----------------------------:|
| setProperty  |     Нет      |       boolean        |     Текущий объект      | Является свойством инфоблока |

## Регистрация карты импорта

````php
use Sholokhov\Exchange\Fields;
use Sholokhov\BitrixExchange\Fields\IBlock\IBlockElementField;

$map = [
    (new Field\Field)
         ->setPath('path')
        ->setCode('NEW_KEY')
        ->setMultiple(true)
        ->setTarget($exchange)
        ->setNormalizers(fn($value) => $value + 2)
        ->setKeyField(true),
    (new IBlockElementField)
        ->setPath('...')
        ->setCode('...')
        ->setProperty(true),
];

$exchange->setMap($map);
````