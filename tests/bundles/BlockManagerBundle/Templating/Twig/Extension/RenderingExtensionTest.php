<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use PHPUnit\Framework\TestCase;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;

final class RenderingExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    private $extension;

    public function setUp(): void
    {
        $this->extension = new RenderingExtension();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getFunctions
     */
    public function testGetFunctions(): void
    {
        self::assertNotEmpty($this->extension->getFunctions());
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getTokenParsers
     */
    public function testGetTokenParsers(): void
    {
        self::assertNotEmpty($this->extension->getTokenParsers());
        self::assertContainsOnlyInstancesOf(TokenParserInterface::class, $this->extension->getTokenParsers());
    }
}
