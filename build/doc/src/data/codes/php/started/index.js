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