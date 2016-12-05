<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Page\Layout;
use PHPUnit\Framework\TestCase;

class SharedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new Shared();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Shared::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new Layout(
                array(
                    'shared' => true,
                )
            )
        );

        $this->assertEquals(
            'Yes',
            $this->provider->getValue($item)
        );
    }
}