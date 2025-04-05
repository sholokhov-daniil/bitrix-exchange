# События импорта элементов информационного блока

Класс [Element](https://github.com/sholokhov-daniil/bitrix-exchange/blob/master/src/Target/IBlock/Element.php)

- [onBeforeIBlockElementAdd](#onbeforeiblockelementadd)
- [onAfterIBlockElementAdd](#onafteriblockelementadd)
- [onBeforeIBlockElementUpdate](#onbeforeiblockelementupdate)
- [onAfterIBlockElementUpdate](#onafteriblockelementupdate)

## onBeforeIBlockElementAdd
Событие вызывается перед созданием элемента:

| Название  | Тип данных | Обязательность |          Примечание           |
|:---------:|:----------:|:--------------:|:-----------------------------:|
|  FIELDS   |   array    |       Да       | Значение передается по ссылке | 

Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['MY_FIELD'] = 15;
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
````

> Присутствует возможность отмены добавления значения. Если отменить добавление, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementAdd',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
````

## onAfterIBlockElementAdd
Событие вызывается после добавления элемента:

| Название |                                                                Тип данных                                                                | Обязательность |            Примечание            |
|:--------:|:----------------------------------------------------------------------------------------------------------------------------------------:|:--------------:|:--------------------------------:|
|    ID    |                                                                   int                                                                    |       Да       |      ID созданного элемента      |
|  FIELDS  |                                                                  array                                                                   |       Да       | Массив с добавляемыми значениями |
|  RESULT  | [Sholokhov\Exchange\Messages\Type\DataResult](https://github.com/sholokhov-daniil/exchange/blob/master/src/Messages/Type/DataResult.php) |       Да       |

Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockElementAdd',
    function(Event $event) {
        //...
    }
);
````

## onBeforeIBlockElementUpdate
Событие перед изменением элемента:

| Название | Тип данных | Обязательность |          Примечание           |
|:--------:|:----------:|:--------------:|:-----------------------------:|
|  FIELDS  |   array    |       Да       | Значение передаются по ссылке |

Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['you_field'] = "new_value";
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
````

> Присутствует возможность отмены изменения значения. Если отменить изменение, то в лог файле появится соответствующее сообщение, но в результате работы импорта это не отобразится.

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementUpdate',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
````

## onAfterIBlockElementUpdate
Событие вызывается после обновления элемента сущности и передаются следующие параметры:

| Название |                                                                Тип данных                                                                | Обязательность |
|:--------:|:----------------------------------------------------------------------------------------------------------------------------------------:|:--------------:|
|   ITEM   |                                                                  array                                                                   |       Да       |
|    ID    |                                                                   int                                                                    |       Да       |
|  RESULT  | [Sholokhov\Exchange\Messages\Type\DataResult](https://github.com/sholokhov-daniil/exchange/blob/master/src/Messages/Type/DataResult.php) |       Да       |

Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockElementUpdate',
    function(Event $event) {
        // ...
    }
);
````