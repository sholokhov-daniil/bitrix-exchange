export const json = `
{
    "items": {
        "item": [
            {
                "id": 15,
                "name": "Тут название",
                "color": {
                    "id": 33,
                    "name": "Черный"
                }
            },
            {
                "name": "Тут название2",
                "color": {
                    "id": 12,
                    "name": "Белый"
                }
            }
        ]
    }
}
`;

export const created = `
use Sholokhov\\Exchange\\Source\\Json;

$config = [
    'source_key' => 'items.item',
    'multiple' => true
];
$source = new Source\\Json('https:\\my-web.ru', $config);
foreach($source as $value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
`;

export const result = `
array(3) {
  ["id"]=>
  int(15)
  ["name"]=>
  string(23) "Тут название"
  ["color"]=>
  array(2) {
    ["id"]=>
    int(33)
    ["name"]=>
    string(12) "Черный"
  }
}

array(2) {
  ["name"]=>
  string(24) "Тут название2"
  ["color"]=>
  array(2) {
    ["id"]=>
    int(12)
    ["name"]=>
    string(10) "Белый"
  }
}
`;