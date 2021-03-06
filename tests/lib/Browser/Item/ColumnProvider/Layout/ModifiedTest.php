<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use DateTimeImmutable;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class ModifiedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified
     */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new Modified('d.m.Y H:i:s');
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified::__construct
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified::getValue
     */
    public function testGetValue(): void
    {
        $date = new DateTimeImmutable();
        $date = $date->setDate(2016, 7, 17);
        $date = $date->setTime(18, 15, 42);

        $item = new Item(
            Layout::fromArray(
                [
                    'modified' => $date,
                ]
            )
        );

        self::assertSame(
            '17.07.2016 18:15:42',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem()));
    }
}
