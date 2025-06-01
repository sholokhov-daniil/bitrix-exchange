export const iterable = `

$arr = [
    ['TITLE' => 'Name1'],
    ['TITLE' => 'Name2'],
    // ...
];

$iterator = new EmptyIterator;

$generator = function() {
    yield ['TITLE' => 'Name1'];
    yield ['TITLE' => 'Name2'];
};

$obj = new class implements Iterator {
    public current() {}
    public key() {}
    public next() {}
    public rewind() {}
    public valid() {}
}
`

export const created = `
use Sholokhov\\Exchange\\Fields\\Field;
use Sholokhov\\Exchange\\Target\\IBlock\\Element;

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
        ->setFrom('id')
        ->setTo('XML_ID')
        ->setPrimary(),
    (new Field)
        ->setFrom('name')
        ->setTo('NAME'),
];

$exchange = new Element($options);
$exchange->setMap($map);
$result = $exchange->execute($data);
`