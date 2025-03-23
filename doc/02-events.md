# События обмена
- [AbstractExchange](https://github.com/sholokhov-daniil/bitrix-exchange/blob/master/src/docs/02-events-afterexchange.md)

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