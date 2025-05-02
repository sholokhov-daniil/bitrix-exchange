# События импорта значений списка в свойство информационного блока

Класс [PropertyEnumeration](https://github.com/sholokhov-daniil/bitrix-exchange/blob/v0.200/src/Target/IBlock/Property/PropertyEnumeration.php)

## События
- [onBeforeIBlockPropertyEnumerationAdd](#onbeforeiblockpropertyenumerationadd)
- [onAfterIBlockPropertyEnumerationAdd](#onafteriblockpropertyenumerationadd)
- [onBeforeIBlockPropertyEnumerationUpdate](#onbeforeiblockpropertyenumerationupdate)
- [onAfterIBlockPropertyEnumerationUpdate](#onafteriblockpropertyenumerationupdate)

## onBeforeIBlockPropertyEnumerationAdd

Событие вызывается перед созданием значения списка

### Параметры события

| Название  | Тип данных | Обязательность |          Примечание           |
|:---------:|:----------:|:--------------:|:-----------------------------:|
|  FIELDS   |   array    |       Да       | Значение передается по ссылке |

### Пример подписки на событие
````php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['XML_ID'] = 'my_value';
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
````

> Присутствует возможность отмены добавления значения.
> 
> Если отменить добавление, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.

### Пример отмены добавления

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationAdd',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
````

## onAfterIBlockPropertyEnumerationAdd

Событие вызывается после добавления значения в список

### Параметры события

| Название |                                                                Тип данных                                                                | Обязательность |            Примечание            |
|:--------:|:----------------------------------------------------------------------------------------------------------------------------------------:|:--------------:|:--------------------------------:|
|    ID    |                                                                   int                                                                    |       Да       |      ID созданного элемента      |
|  FIELDS  |                                                                  array                                                                   |       Да       | Массив с добавляемыми значениями |
|  RESULT  | [Sholokhov\Exchange\Messages\Type\DataResult](https://github.com/sholokhov-daniil/exchange/blob/master/src/Messages/Type/DataResult.php) |       Да       |

### Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockPropertyEnumerationAdd',
    function(Event $event) {
        //...
    }
);
````


## onBeforeIBlockPropertyEnumerationUpdate

Событие перед изменением элемента

### Параметры события

| Название | Тип данных | Обязательность |          Примечание           |
|:--------:|:----------:|:--------------:|:-----------------------------:|
|  FIELDS  |   array    |       Да       | Значение передаются по ссылке |
|    ID    |    int     |       Да       |   ID обновляемого элемента    |

### Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['XML_ID'] = "new_value";
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
````

> Присутствует возможность отмены изменения
> 
> Если отменить изменение, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.

### Пример отмены добавления

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationUpdate',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
````

## onAfterIBlockPropertyEnumerationUpdate

Событие вызывается после обновления значения списка

### Параметры события

| Название |                                                                Тип данных                                                                | Обязательность |
|:--------:|:----------------------------------------------------------------------------------------------------------------------------------------:|:--------------:|
|  FIELDS  |                                                                  array                                                                   |       Да       |
|    ID    |                                                                   int                                                                    |       Да       |
|  RESULT  | [Sholokhov\Exchange\Messages\Type\DataResult](https://github.com/sholokhov-daniil/exchange/blob/master/src/Messages/Type/DataResult.php) |       Да       |

### Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockPropertyEnumerationUpdate',
    function(Event $event) {
        // ...
    }
);
````

[![Back](https://img.shields.io/badge/События_свойств_информационного_блока-blue?style=for-the-badge)](https://github.com/sholokhov-daniil/exchange/blob/v0.200/doc/events/iblock/properties/main.md)