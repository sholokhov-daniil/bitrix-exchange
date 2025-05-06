export default `
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'eventName',
    $callback
);
`