export const BeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeHighloadblockElementAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['MY_FIELD'] = 15;
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
`;

export const CancelBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeHighloadblockElementAdd',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
`;

export const AfterAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterHighloadblockElementAdd',
    function(Event $event) {
        //...
    }
);
`;

export const BeforeUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeHighloadblockElementUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['you_field'] = "new_value";
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
`;

export const CancelBeforeUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeHighloadblockElementUpdate',
    function(Event $event) {        
        return new EventResult(EventResult::ERROR, $event->getParameters());
    }
);
`;

export const AfterUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterHighloadblockElementUpdate',
    function(Event $event) {
        // ...
    }
);
`;