<?php

namespace Netgen\BlockManager\Tests\View\View\BlockView;

use Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate;
use PHPUnit\Framework\TestCase;
use Twig_Template;
use Exception;

class ContextualizedTwigTemplateTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate::renderBlock
     */
    public function testRenderBlock()
    {
        $templateMock = $this->createMock(Twig_Template::class);

        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->returnCallback(
                function ($blockName) {
                    echo 'rendered';
                }
            )
        );

        $template = new ContextualizedTwigTemplate($templateMock);

        $this->assertEquals('rendered', $template->renderBlock('block_name'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate::renderBlock
     * @expectedException \Exception
     */
    public function testRenderBlockWithException()
    {
        $templateMock = $this->createMock(Twig_Template::class);

        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->throwException(new Exception()));

        $template = new ContextualizedTwigTemplate($templateMock);
        $template->renderBlock('block_name');
    }
}