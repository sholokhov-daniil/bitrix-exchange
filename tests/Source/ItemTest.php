<?php

namespace Sholokhov\Exchange\Source;

use Bitrix\Main\Diag\Debug;
use stdClass;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testCurrent(mixed $item): void
    {
        $source = new Item($item);

        $this->assertSame($item, $source->current());

        $source->next();
        $this->assertNotSame($item, $source->current());

        $source->rewind();
        $this->assertSame($item, $source->current());
    }

    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testNext(mixed $item): void
    {
        $source = new Item($item);
        $source->next();
        $this->assertNotSame($item, $source->current());
    }

    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testKey(mixed $item): void
    {
        $source = new Item($item);
        $this->assertSame(0, $source->key());
        $source->next();
        $this->assertSame(null, $source->key());
    }

    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testValid(mixed $item): void
    {
        $source = new Item($item);
        $this->assertTrue($source->valid());
        $source->next();
        $this->assertFalse($source->valid());
    }

    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testRewind(mixed $item): void
    {
        $source = new Item($item);
        $source->next();
        $source->rewind();
        $this->assertSame($item, $source->current());
    }

    /**
     * @param mixed $item
     * @return void
     * @dataProvider itemsProvider
     */
    public function testIterable(mixed $item): void
    {
        $source = new Item($item);

        foreach ($source as $value) {
            $this->assertSame($item, $value);
        }
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