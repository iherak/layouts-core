<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;
use DateTime;

class ModifiedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new Modified('d.m.Y H:i:s');
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified::__construct
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Modified::getValue
     */
    public function testGetValue()
    {
        $date = new DateTime();
        $date->setDate(2016, 7, 17);
        $date->setTime(18, 15, 42);

        $item = new Item(
            new Layout(
                array(
                    'modified' => $date,
                )
            )
        );

        $this->assertEquals(
            '17.07.2016 18:15:42',
            $this->provider->getValue($item)
        );
    }
}