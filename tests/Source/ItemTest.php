<?php

namespace Sholokhov\Exchange\Source;

use stdClass;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testIterable(mixed $item): void
    {
        $source = new Item($item);
        $this->assertSame($item, $source->fetch());
    }

    public static function itemsProvider(): array
    {
        $std = new stdClass();
        $std->name = 'test';

        $dynamicClass = new class {
            private string $name = 'Hello';
        };

        return [
            [12],
            [false],
            [['zz', 'ss']],
            ['hello'],
            [true],
            [33.56],
            [$std],
            [$dynamicClass],
            [fn() => 'Hello world']
        ];
    }
}