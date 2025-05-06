export default `
use Sholokhov\\Exchange\\Fields;
use Sholokhov\\BitrixExchange\\Target\\IBlock\\Element;

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
    (new Fields\\Field)
        ->setPath('id')
        ->setCode('XML_ID')
        ->setPrimary(),
    (new Fields\\Field)
        ->setPath('name')
        ->setCode('NAME'),
];

$exchange = new Element;
$exchange->setMap($map);
$result = $exchange->execute($data);
`