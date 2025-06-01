import {crossMark, isRight} from "@/utils/emojis";

export const example = `
use Sholokhov\\Exchange\\Source\\Xml;

$source = new Xml('https:\\\\my-web.com');

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
use Sholokhov\\Exchange\\Source\\Xml;

$source = new Xml('https:\\\\my-web.com');
$source->setRootTag('items');

foreach($source as $value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
`;

export const result = `
array(4) {
  ["item_attribute_id"]=>
  string(2) "15"
  ["item_name"]=>
  string(23) "Тут название"
  ["item_color"]=>
  string(12) "Черный"
  ["item_color_attribute_id"]=>
  string(2) "33"
}

array(3) {
  ["item_name"]=>
  string(24) "Тут название2"
  ["item_color"]=>
  string(10) "Белый"
  ["item_color_attribute_id"]=>
  string(2) "12"
}
`;

export const xmlDepth = `
<?xml version="1.0"?>
<data>
    <handbook>
        <catalog>
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
        </catalog>
    </handbook>
</data>
`;

export const rootTagDepth = `
use Sholokhov\\Exchange\\Source\\Xml;

$source = new Xml('https:\\\\my-web.com');
$source->setRootTag('items');
$source->setRootTagDepth(3);

foreach($source as $value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
`;

export const resultDepth = `
array(4) {
  ["item_attribute_id"]=>
  string(2) "15"
  ["item_name"]=>
  string(23) "Тут название"
  ["item_color"]=>
  string(12) "Черный"
  ["item_color_attribute_id"]=>
  string(2) "33"
}
array(3) {
  ["item_name"]=>
  string(24) "Тут название2"
  ["item_color"]=>
  string(10) "Белый"
  ["item_color_attribute_id"]=>
  string(2) "12"
}
`;

export const depthPractices = `
// ${isRight} Хорошо
$source->setRootTag('items');
$source->setRootTagDepth(3);
$exchange->execute($source);


// ${crossMark} Плохо
$source->setRootTag('items');
$exchange->execute($source);
`;