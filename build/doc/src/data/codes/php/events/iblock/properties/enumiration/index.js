export const BeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['XML_ID'] = 'my_value';
        
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
    'onBeforeIBlockPropertyEnumerationAdd',
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
    'onAfterIBlockPropertyEnumerationAdd',
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
    'onBeforeIBlockPropertyEnumerationUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['FIELDS']['XML_ID'] = "new_value";
        
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
    'onBeforeIBlockPropertyEnumerationUpdate',
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
    'onAfterIBlockPropertyEnumerationUpdate',
    function(Event $event) {
        // ...
    }
);
`;