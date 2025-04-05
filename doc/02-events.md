# События обмена
- [Базовый класс обмена (Sholokhov\Exchange\Exchange)](https://github.com/sholokhov-daniil/exchange/blob/master/doc/02-events-exchange.md)
- [Импорт элементов Highloadblock (Sholokhov\Exchange\Target\Highloadblock\Element)](https://github.com/sholokhov-daniil/bitrix-exchange/blob/master/src/Target/Highloadblock/Element.php)
- [Импорт элементов информационного блока (Sholokhov\Exchange\Target\IBlock\Element)](https://github.com/sholokhov-daniil/bitrix-exchange/blob/master/src/Target/IBlock/Element.php)

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
