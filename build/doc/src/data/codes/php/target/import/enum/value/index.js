import {crossMark, isRight} from "@/utils/emojis";

export const exampleDataFormat = `
// ${isRight} Верно
$source = [
    [
        'value' => '....',
    ],
    [
        'value' => '....'
    ]
];

// ${crossMark} Неверно
$source = [
    [
        'value' => [
            '...',
            '...',
        ]
    ],
    [
        'value' => [
            '...',
            '...',
        ]
    ]
];
`;


export const exampleStart = `
use Sholokhov\\BitrixExchange\\Fields\\Field;
use Sholokhov\\BitrixExchange\\Target\\UserFields\\Enumeration;

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
        ->setTo('VALUE'),
];

$options = [
    'entity_id' => 'HLBLOCK_1',
    'property_code' => 'UF_COLOR'
];

$exchange = new Enumeration($options);
$exchange->setMap($map);
$exchange->execute($data);

`;

export const modifBefureUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeUFEnumerationUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['VALUE'] = 'Теперь такое название';
        
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
    'onBeforeUFEnumerationUpdate',
    function(Event $event) {
        /** @var $exchange Enumeration **/
        $exchange = $event->getParameter('exchange');
        $parameters = $event->getParameters();
        
        if (
            $exchange->getEntityId() === 'HLBLOCK_1'
            && $exchange->getPropertyCode() === 'UF_COLOR'
            && $event->getParameters()['XML_ID'] === 'black'
        ) {
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
    'onBeforeUFEnumerationUpdate',
    fn() => new EventResult(EventResult::ERROR, ['errors' => ['Ошибка 1', 'Ошибка 2']])
);
`;

export const afterUpdate = `
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterUFEnumerationUpdate',
    fn() => CEvent::Send(....)
);
`;


export const modifBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeUFEnumerationAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['VALUE'] = 'Теперь такое название';
        
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
    'onBeforeUFEnumerationAdd',
    fn() => throw new ExchangeItemStoppedException('Так захотелось')
);
`;

export const errorBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeUFEnumerationAdd',
    fn() => new EventResult(EventResult::ERROR, ['errors' => ['Ошибка 1', 'Ошибка 2']])
);
`;

export const afterAdd = `
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterUFEnumerationAdd',
    fn() => CEvent::Send(....)
);
`;