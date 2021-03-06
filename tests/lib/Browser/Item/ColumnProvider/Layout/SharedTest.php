<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class SharedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared
     */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new Shared();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared::getValue
     */
    public function testGetValue(): void
    {
        $item = new Item(
            Layout::fromArray(
                [
                    'shared' => true,
                ]
            )
        );

        self::assertSame(
            'Yes',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared::getValue
     */
    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem()));
    }
}
