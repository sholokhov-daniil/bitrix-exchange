# События импорта элементов Highloadblock

Класс [Element](https://github.com/sholokhov-daniil/bitrix-exchange/blob/v0.200/src/Target/Highloadblock/Element.php)

- [onBeforeHighloadblockElementAdd](#onbeforehighloadblockelementadd)
- [onAfterHighloadblockElementAdd](#onafterhighloadblockelementadd)
- [onBeforeHighloadblockElementUpdate](#onbeforehighloadblockelementupdate)
- [onAfterHighloadblockElementUpdate](#onafterhighloadblockelementupdate)

## onBeforeHighloadblockElementAdd
Событие вызывается перед созданием элемента справочника(Highloadblock):

| Название | Тип данных | Обязательность |          Примечание           |
|:--------:|:----------:|:--------------:|:-----------------------------:|
|  FIELDS  |   array    |       Да       | Значение передается по ссылке | 

Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeHighloadblockElementAdd',
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
    'onBeforeHighloadblockElementAdd',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
````

## onAfterHighloadblockElementAdd
Событие вызывается после добавления элемента справочника:

| Название | Тип данных | Обязательность |            Примечание            |
|:--------:|:----------:|:--------------:|:--------------------------------:|
|    ID    |    int     |       Да       |      ID созданного элемента      |
|  FIELDS  |   array    |       Да       | Массив с добавляемыми значениями |

Пример подписки на событие

````php
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Sholokhov\Exchange\Repository\RepositoryInterface;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterHighloadblockElementAdd',
    function(Event $event) {
        //...
    }
);
````

## onBeforeHighloadblockElementUpdate
Событие перед изменением элемента справочника:

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
    'onBeforeHighloadblockElementUpdate',
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
    'onBeforeHighloadblockElementUpdate',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
````

## onAfterHighloadblockElementUpdate
Событие вызывается после обновления элемента сущности и передаются следующие параметры:

| Название |                                                                Тип данных                                                                | Обязательность |
|:--------:|:----------------------------------------------------------------------------------------------------------------------------------------:|:--------------:|
|  FIELDS  |                                                                  array                                                                   |       Да       |
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
    'onAfterHighloadblockElementUpdate',
    function(Event $event) {
        $itemID = $event->getParameter('ID');
        // ...
    }
);
````