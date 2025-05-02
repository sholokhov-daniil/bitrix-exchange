# События обмена
- [Базовый класс обмена (Sholokhov\Exchange\Exchange)](https://github.com/sholokhov-daniil/exchange/blob/master/doc/events-exchange.md)
- [Обмен данных с информационным блоком](https://github.com/sholokhov-daniil/exchange/blob/v0.200/doc/events/iblock/main.md)

Все события модуля регистрируются в модуле [bitrix](https://dev.1c-bitrix.ru/api_d7/bitrix/main/EventManager/index.php)

Все события вызываются от модуля `sholokhov.exchange`

Пример подписки на событие

````php
use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'eventName',
    $callback
);
````
