<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\View\RuleTargetView;
use PHPUnit\Framework\TestCase;

class RuleTargetViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    protected $target;

    /**
     * @var \Netgen\BlockManager\View\View\RuleTargetViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->target = new Target(array('id' => 42));

        $this->view = new RuleTargetView($this->target);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('target' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getTarget
     */
    public function testGetTarget()
    {
        self::assertEquals($this->target, $this->view->getTarget());
        self::assertEquals(
            array(
                'param' => 'value',
                'target' => $this->target,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('rule_target_view', $this->view->getIdentifier());
    }
}