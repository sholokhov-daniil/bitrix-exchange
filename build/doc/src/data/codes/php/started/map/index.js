import {crossMark, isRight} from "@/utils/emojis";

export const mapBase = `
use Sholokhov\\BitrixExchange\\Fields\\Field;

$source = [
    [
        'name' => 'Название элемента',
        'price' => 15.2
    ],
    [
        'name' => 'Еще какой-то товар',
        'price' => 17.2,
    ],
    [
        'name' => 'Хороший товар',
        'price' => 15.2,
        'image' => 'https://example/upload/image.png'
    ]
];

$map = [
    (new Field)
        ->setFrom('name')
        ->setTo('NAME')
        ->setPrimary(),
    (new Field)
        ->setFrom('image')
        ->setTo('PREVIEW_PICTURE')
];

$exchange->setMap($map);
`;

export const codeNormalize = `
use Sholokhov\\BitrixExchange\\Fields\\Field;
use Sholokhov\\BitrixExchange\\Fields\\FieldInterface;

$data = [
    [
        'user' => 'Иванов|34 года',
    ],
    [
        'user' => 'Пупкин|18'
    ]
];

$nameField = new Field;
$nameField->setFrom('user');
$nameField->setTo('USER')
$nameField->setNormalizer(fn(mixed $value, FieldInterface $field) => stristr($value, '|', true));
`;

export const codePreparation = `
use Sholokhov\\BitrixExchange\\Fields\\Field;
use Sholokhov\\BitrixExchange\\Messages\\Type\\DataResult;

use Bitrix\\Main\\Error;
use Bitrix\\Main\\Web\\HttpClient;


$field->setPreparation(function(mixed $value, FieldInterface $field) {
    $result = new DataResult;
    $url = 'https://myweb.com/upload/' . $value;
    
    $options = [
        'headers' => [
            'Authorization: OAuth MYToken'
        ]
    ];
    
    $http = new HttpClient($options);
    $tmp = CFile::GetTempName('', 'tmp.' . md5(mt_rand())); 
    
    if ($http->download($url, $tmp)) {
        $result->setData(
            CFile::MakeFileArray($tmp)
        );
    } else {
        $result->addError(new Error('Ошибка получения изображения: ' . $url));
    }
    
    return $result;
});
`;

export const codeChildren = `
$source = [
    [
        'name' => 'Название элемента',
        'images' => [
            'image' => [
                [
                    'sdn' => 'https://web.ru/upload/1.png',
                    'name' => '1.png',
                ],
                [   
                    'sdn' => 'https://web.ru/upload/2.png',
                    'name' => '2.png'
                ]
            ]
        ]
    ],
    // ...
];

// ${isRight} Работает 
$field = new Field;
$field->setFrom('images.image');
$field->setChildren(
    (new Field)->setFrom('sdn')
);

// ${crossMark} Не работает
$field = new Field;
$field->setFrom('images.image.sdn');
`;

export const codeChildrenLvl = `
$source = [
    [
        'name' => 'Название элемента',
        'images' => [
            'image' => [
                [
                    'sdn' => [
                        [
                            'path' => 'https://web.ru/upload/1.png'
                            'name' => '1.png',
                        ]
                    ]
                ],
                [   
                    'sdn' => [
                        [
                            'path' => 'https://web.ru/upload/2.png'
                            'name' => '2.png',
                        ]
                    ]
                ]
            ]
        ]
    ],
    // ...
];

$field = new Field;
$field->setFrom('images.image');
$field->setChildren(
    (new Field)
        ->setFrom('sdn')
        ->setChildren(
            (new Field)
                ->setFrom('path')
        )
);
`;

export const codeChildrenFirst = `
$source = [
    [
        'name' => 'Название элемента',
        'images' => [
            'image' => [
                [
                    'sdn' => 'https://web.ru/upload/1.png',
                    'name' => '1.png',
                ],
                [   
                    'sdn' => 'https://web.ru/upload/2.png',
                    'name' => '2.png'
                ]
            ]
        ]
    ],
    // ...
];

$field = new Field;
// т.к. в image хранится перечисляемый массив, то нам достаточно указать номер ключа
$field->setFrom('images.image.0.sdn');
`;