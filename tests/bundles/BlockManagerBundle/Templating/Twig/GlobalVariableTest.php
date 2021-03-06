<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Exception;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class GlobalVariableTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $pageLayoutResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
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

    public function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);
        $this->layoutResolverMock = $this->createMock(LayoutResolverInterface::class);
        $this->pageLayoutResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->viewBuilderMock = $this->createMock(ViewBuilderInterface::class);

        $this->requestStack = new RequestStack();

        $this->globalVariable = new GlobalVariable(
            $this->configMock,
            $this->layoutResolverMock,
            $this->pageLayoutResolverMock,
            $this->viewBuilderMock,
            $this->requestStack,
            true
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate(): void
    {
        $this->pageLayoutResolverMock
            ->expects(self::once())
            ->method('resolvePageLayout')
            ->will(self::returnValue('pagelayout.html.twig'));

        self::assertSame(
            'pagelayout.html.twig',
            $this->globalVariable->getPageLayoutTemplate()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayout(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(
                self::returnValue(
                    Rule::fromArray(['layout' => $layout])
                )
            );

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->will(
                self::returnValue(
                    new LayoutView($layout)
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layout, $this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(self::returnValue(null));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutView(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(
                self::returnValue(
                    Rule::fromArray(['layout' => $layout])
                )
            );

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->will(self::returnValue($layoutView));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layoutView, $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoRequest(): void
    {
        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithException(): void
    {
        $subRequest = Request::create('/');
        $subRequest->attributes->set('exception', new Exception());
        $this->requestStack->push($subRequest);

        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(
                self::returnValue(
                    Rule::fromArray(['layout' => $layout])
                )
            );

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->will(self::returnValue($layoutView));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layoutView, $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(self::returnValue(null));

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertFalse($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRule(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();
        $rule = Rule::fromArray(['layout' => $layout]);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(
                self::returnValue(
                    Rule::fromArray(['layout' => $layout])
                )
            );

        $layoutView = new LayoutView($layout);
        $layoutView->addParameter('rule', $rule);

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->will(self::returnValue($layoutView));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($rule, $this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(self::returnValue(null));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplate(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);
        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(
                self::returnValue(
                    Rule::fromArray(['layout' => $layout])
                )
            );

        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->with(self::identicalTo($layout))
            ->will(self::returnValue($layoutView));

        $this->pageLayoutResolverMock
            ->expects(self::never())
            ->method('resolvePageLayout');

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('ngbmLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithAlreadyExistingResolvedLayout(): void
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $request = Request::create('/');
        $request->attributes->set('ngbmLayoutView', $layoutView);
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->expects(self::at(0))
            ->method('resolvePageLayout')
            ->will(self::returnValue('pagelayout.html.twig'));

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('ngbmLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoRequest(): void
    {
        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->expects(self::at(0))
            ->method('resolvePageLayout')
            ->will(self::returnValue('pagelayout.html.twig'));

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithException(): void
    {
        $request = Request::create('/');
        $request->attributes->set('exception', new Exception());
        $this->requestStack->push($request);

        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(
                self::returnValue(
                    Rule::fromArray(['layout' => $layout])
                )
            );

        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->with(self::identicalTo($layout))
            ->will(self::returnValue($layoutView));

        $this->pageLayoutResolverMock
            ->expects(self::never())
            ->method('resolvePageLayout');

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('ngbmExceptionLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithExceptionWithAlreadyExistingResolvedLayout(): void
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $request = Request::create('/');
        $request->attributes->set('exception', new Exception());
        $request->attributes->set('ngbmExceptionLayoutView', $layoutView);
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->expects(self::at(0))
            ->method('resolvePageLayout')
            ->will(self::returnValue('pagelayout.html.twig'));

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('ngbmExceptionLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->will(self::returnValue(null));

        $this->pageLayoutResolverMock
            ->expects(self::once())
            ->method('resolvePageLayout')
            ->will(self::returnValue('pagelayout.html.twig'));

        self::assertSame(
            'pagelayout.html.twig',
            $this->globalVariable->getLayoutTemplate()
        );

        self::assertFalse(
            $request->attributes->get('ngbmLayoutView')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getConfig
     */
    public function testGetConfig(): void
    {
        self::assertSame($this->configMock, $this->globalVariable->getConfig());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getDebug
     */
    public function testGetDebug(): void
    {
        self::assertTrue($this->globalVariable->getDebug());
    }
}
