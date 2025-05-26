export const exampleStart = `
use Sholokhov\\BitrixExchange\\Fields\\Field;
use Sholokhov\\BitrixExchange\\Target\\IBlock\\Section;

$data = [
    [
        'code' => 'black',
        'title' => 'Черный'
    ],
    [
        'code' => 'white',
        'title' => 'Белый'
    ]
];

$map = [
    (new Field)
        ->setFrom('code')
        ->setTo('XML_ID')
        ->setPrimary(),
    (new Field)
        ->setFrom('title'),
        ->setTo('NAME'),
];

$options = [
    'iblock_id' => 3,
];

$exchange = new Section($options);
$exchange->setMap($map);
$exchange->execute($data);
`;

export const modifBefureUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockSectionUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['fields']['NAME'] = 'Теперь такое название';
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
`;

export const medifBeforeUpdateCancel = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

use Sholokhov\\BitrixExchange\\Target\\UserFields\\Enumeration;
use Sholokhov\\BitrixExchange\\Exception\\Target\\ExchangeItemStoppedException;

/**
 *  Отмена изменения элемента нужного свойства нужной сущности 
 *  и записью в лог стандартного(системного) сообщения
**/
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockSectionUpdate',
    function(Event $event) {
        /** @var $exchange Enumeration **/
        $exchange = $event->getParameter('exchange');
        $parameters = $event->getParameters();
        
        if ($exchange->getIBlockID() === 3) {
            throw new ExchangeItemStoppedException('Так захотелось');
        }
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
`;

export const errorBeforeUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockSectionUpdate',
    fn() => new EventResult(EventResult::ERROR, ['errors' => ['Ошибка 1', 'Ошибка 2']])
);
`;



export const afterUpdate = `
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockElementUpdate',
    fn() => CEvent::Send(....)
);
`;


export const modifBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['fields']['NAME'] = 'Теперь такое название';
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
`;

export const modifBeforeAddCancel = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementAdd',
    fn() => throw new ExchangeItemStoppedException('Так захотелось')
);
`;

export const errorBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementAdd',
    fn() => new EventResult(EventResult::ERROR, ['errors' => ['Ошибка 1', 'Ошибка 2']])
);
`;

export const afterAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockElementAdd',
    fn() => CEvent::Send(....)
);
`;

export const beforeDeactivate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockElementsDeactivate',
    function(Event $event) {
        ...
    }
);
`;