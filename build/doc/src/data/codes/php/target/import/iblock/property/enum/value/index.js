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
use Sholokhov\\Exchange\\Fields\\Field;
use Sholokhov\\Exchange\\Target\\IBlock\\Property\\PropertyEnumeration;

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
    'iblock_id' => '1',
    'property_code' => 'COLOR'
];

$exchange = new PropertyEnumeration($options);
$exchange->setMap($map);
$exchange->execute($data);

`;

export const modifBefureUpdate = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationUpdate',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['fields']['VALUE'] = 'Теперь такое название';
        
        return new EventResult(EventResult::SUCCESS, $parameters);
    }
);
`;

export const medifBeforeUpdateCancel = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

use Sholokhov\\Exchange\\Target\\UserFields\\Enumeration;
use Sholokhov\\Exchange\\Exception\\Target\\ExchangeItemStoppedException;

/**
 *  Отмена изменения элемента нужного свойства нужной сущности 
 *  и записью в лог стандартного(системного) сообщения
**/
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationUpdate',
    function(Event $event) {
        /** @var $exchange Enumeration **/
        $exchange = $event->getParameter('exchange');
        $parameters = $event->getParameters();
        
        if (
            $exchange->getIBlockID() === 1'
            && $exchange->getPropertyCode() === 'COLOR'
            && $event->getParameters()['fields']['XML_ID'] === 'black'
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
    'onBeforeIBlockPropertyEnumerationUpdate',
    fn() => new EventResult(EventResult::ERROR, ['errors' => ['Ошибка 1', 'Ошибка 2']])
);
`;

export const afterUpdate = `
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockPropertyEnumerationUpdate',
    fn() => CEvent::Send(....)
);
`;


export const modifBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;

EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationAdd',
    function(Event $event) {
        $parameters = &$event->getParameters();
        $parameters['fields']['VALUE'] = 'Теперь такое название';
        
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
    'onBeforeIBlockPropertyEnumerationAdd',
    fn() => throw new ExchangeItemStoppedException('Так захотелось')
);
`;

export const errorBeforeAdd = `
use Bitrix\\Main\\Event;
use Bitrix\\Main\\EventResult;
use Bitrix\\Main\\EventManager;
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onBeforeIBlockPropertyEnumerationAdd',
    fn() => new EventResult(EventResult::ERROR, ['errors' => ['Ошибка 1', 'Ошибка 2']])
);
`;

export const afterAdd = `
EventManager::getInstance()->addEventHandler(
    'sholokhov.exchange',
    'onAfterIBlockPropertyEnumerationAdd',
    fn() => CEvent::Send(....)
);
`;