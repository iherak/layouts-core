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

        $this->view = new RuleTargetView(
            array(
                'target' => $this->target,
            )
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('target', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleTargetView::getTarget
     */
    public function testGetTarget()
    {
        $this->assertEquals($this->target, $this->view->getTarget());
        $this->assertEquals(
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
        $this->assertEquals('rule_target_view', $this->view->getIdentifier());
    }
}