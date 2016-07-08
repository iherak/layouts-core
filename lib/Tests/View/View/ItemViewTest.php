<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\View\View\ItemView;
use PHPUnit\Framework\TestCase;

class ItemViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\Item
     */
    protected $item;

    /**
     * @var \Netgen\BlockManager\View\View\ItemViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->item = new Item();

        $this->view = new ItemView($this->item, 'view_type');
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('item' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::__construct
     * @covers \Netgen\BlockManager\View\View\ItemView::getItem
     */
    public function testGetItem()
    {
        self::assertEquals($this->item, $this->view->getItem());
        self::assertEquals(
            array(
                'param' => 'value',
                'item' => $this->item,
                'viewType' => 'view_type',
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getViewType
     */
    public function testGetViewType()
    {
        self::assertEquals('view_type', $this->view->getViewType());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('item_view', $this->view->getIdentifier());
    }
}