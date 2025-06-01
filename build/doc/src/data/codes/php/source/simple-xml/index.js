export const example = `
use Sholokhov\\Exchange\\Source\\SimpleXml;

$source = new SimpleXml('https:\\\\my-web.com');

foreach($source as $value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
`;

export const xml = `
<?xml version="1.0"?>
<data>
    <items>
        <item id="15">
            <name>Тут название</name>
            <color id="33">Черный</color>
        </item>
        <item>
            <name>Тут название2</name>
            <color id="12">Белый</color>
        </item>
    </items>
</data>
`;

export const rootTag = `
use Sholokhov\\Exchange\\Source\\SimpleXml;

$source = new SimpleXml('https:\\\\my-web.com');
$source->setRootTag('items.item');

foreach($source as $value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
`;

export const result = `
array(3) {
  ["@id"]=>
  int(15)
  ["name"]=>
  string(23) "Тут название"
  ["color"]=>
  array(2) {
    ["@id"]=>
    int(33)
    ["#"]=>
    string(12) "Черный"
  }
}
array(2) {
  ["name"]=>
  string(24) "Тут название2"
  ["color"]=>
  array(2) {
    ["@id"]=>
    int(12)
    ["#"]=>
    string(10) "Белый"
  }
}
`;