export default `
use Sholokhov\\BitrixExchange\\Fields\\Field;
use Sholokhov\\BitrixExchange\\Target\\IBlock\\Element;

$options = [
    'iblock_id' => 13
];

$data = [
    [
        'id' => 56,
        'name' => 'Какой-то элемент',
    ],
    [
        'id' => 15,
        'name' => 'Какой-то элемент 2',
    ]
];

$map = [
    (new Field)
        ->setOut('id')
        ->setIn('XML_ID')
        ->setPrimary(),
    (new Field)
        ->setOut('name')
        ->setIn('NAME'),
];

$exchange = new Element($options);
$exchange->setMap($map);
$result = $exchange->execute($data);
`

export const deactivate = `
$config = [
    'deactivate' => true,
];

new Exchange($config);
`;

export const setResult = `
$exchange = new Exchange;
$exchange->setResultRepository($callback);
`


export const map = `
use Sholokhov\\BitrixExchange\\Fields\\Field;
use Sholokhov\\BitrixExchange\\Fields\\IBlock\\IBlockElementField;

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
        ->setOut('name')
        ->setIn('NAME')
        ->setPrimary(),
    (new IBlockElementField)
        ->setOut('image')
        ->setIn('MORE_PHOTO')
];

$exchange->setMap($map);
$exchange->execute($source);
`;
export const logger = `
/** @var Psr\\Log\\LoggerInterface $logger **/
$logger = new YourLogger;

$exchange = new Exchange;
$exchange->setLogger($logger);
`;

export const preparation = `
// Будет вызываться вторым
$exchange->addPrepared($myPreparation);

// Будет вызываться первым
$exchange->addPrepared($preparation2);
`;