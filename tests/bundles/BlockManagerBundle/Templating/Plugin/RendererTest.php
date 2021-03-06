<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Plugin;

use Exception;
use Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer;
use Netgen\Bundle\BlockManagerBundle\Templating\Plugin\SimplePlugin;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class RendererTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $twigMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\RendererInterface
     */
    private $renderer;

    public function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->renderer = new Renderer(
            $this->twigMock,
            [
                'plugin' => [
                    new SimplePlugin('template1.html.twig'),
                    new SimplePlugin('template2.html.twig', ['param2' => 'value2', 'param' => 'value3']),
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPlugins(): void
    {
        $this->twigMock
            ->expects(self::at(0))
            ->method('display')
            ->with(
                self::identicalTo('template1.html.twig'),
                self::identicalTo(['param' => 'value'])
            )
            ->will(
                self::returnCallback(
                    function (): void {
                        echo 'rendered1';
                    }
                )
            );

        $this->twigMock
            ->expects(self::at(1))
            ->method('display')
            ->with(
                self::identicalTo('template2.html.twig'),
                self::identicalTo(['param2' => 'value2', 'param' => 'value3'])
            )
            ->will(
                self::returnCallback(
                    function (): void {
                        echo 'rendered2';
                    }
                )
            );

        self::assertSame('rendered1rendered2', $this->renderer->renderPlugins('plugin', ['param' => 'value']));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPluginsWithUnknownPlugin(): void
    {
        $this->twigMock
            ->expects(self::never())
            ->method('display');

        self::assertSame('', $this->renderer->renderPlugins('unknown'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Plugin\Renderer::renderPlugins
     */
    public function testRenderPluginsWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception message');

        $this->twigMock
            ->expects(self::at(0))
            ->method('display')
            ->will(self::throwException(new Exception('Test exception message')));

        $this->renderer->renderPlugins('plugin');
    }
}
