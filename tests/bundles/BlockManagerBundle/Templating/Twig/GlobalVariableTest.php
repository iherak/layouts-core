<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GlobalVariableTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $pageLayoutResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $viewBuilderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    public function setUp()
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);
        $this->layoutResolverMock = $this->createMock(LayoutResolverInterface::class);
        $this->pageLayoutResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->viewBuilderMock = $this->createMock(ViewBuilderInterface::class);

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->globalVariable = new GlobalVariable(
            $this->configMock,
            $this->layoutResolverMock,
            $this->pageLayoutResolverMock,
            $this->viewBuilderMock,
            $this->requestStack
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate()
    {
        $this->pageLayoutResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $this->assertEquals(
            'pagelayout.html.twig',
            $this->globalVariable->getPageLayoutTemplate()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayout()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will(
                $this->returnValue(
                    array(new Rule(array('layout' => new Layout())))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new Layout(), $this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will($this->returnValue(array()));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolverExecuted()
    {
        $this->assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutView()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will(
                $this->returnValue(
                    array(new Rule(array('layout' => new Layout())))
                )
            );

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->will(
                $this->returnValue(
                    new LayoutView(array('layout' => new Layout()))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new LayoutView(array('layout' => new Layout())), $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will($this->returnValue(array()));

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertFalse($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolverExecuted()
    {
        $this->assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRule()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will(
                $this->returnValue(
                    array(new Rule(array('layout' => new Layout())))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new Rule(array('layout' => new Layout())), $this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will($this->returnValue(array()));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolverExecuted()
    {
        $this->assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     */
    public function testGetLayoutTemplate()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will(
                $this->returnValue(
                    array(new Rule(array('layout' => new Layout())))
                )
            );

        $layoutView = new LayoutView(array('layout' => new Layout()));
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo(new Layout()))
            ->will($this->returnValue($layoutView));

        $this->pageLayoutResolverMock
            ->expects($this->never())
            ->method('resolvePageLayout');

        $this->assertEquals('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        $this->assertEquals(
            $layoutView,
            $this->requestStack->getCurrentRequest()->attributes->get('ngbmView')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     */
    public function testGetLayoutTemplateWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRules')
            ->will($this->returnValue(array()));

        $this->pageLayoutResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $this->assertEquals(
            'pagelayout.html.twig',
            $this->globalVariable->getLayoutTemplate()
        );

        $this->assertFalse(
            $this->requestStack->getCurrentRequest()->attributes->has('ngbmView')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->globalVariable->getConfig());
    }
}
