<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class LayoutIdTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId
     */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new LayoutId();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'id' => 42,
                ]
            )
        );

        self::assertSame('42', $this->provider->getValue($item));
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem()));
    }
}
