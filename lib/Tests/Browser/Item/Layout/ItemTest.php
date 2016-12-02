<?php

namespace Netgen\BlockManager\Tests\Browser\Item\Layout;

use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Page\Layout;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected $layout;

    /**
     * @var \Netgen\BlockManager\Browser\Item\Layout\Item
     */
    protected $item;

    public function setUp()
    {
        $this->layout = new Layout(array('id' => 42, 'name' => 'My layout'));

        $this->item = new Item($this->layout);
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::__construct
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ngbm_layout', $this->item->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::getName
     */
    public function testGetName()
    {
        $this->assertEquals('My layout', $this->item->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::getParentId
     */
    public function testGetParentId()
    {
        $this->assertEquals(0, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::isVisible
     */
    public function testIsVisible()
    {
        $this->assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Layout\Item::getLayout
     */
    public function testGetLayout()
    {
        $this->assertEquals($this->layout, $this->item->getLayout());
    }
}