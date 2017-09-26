<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

class HelpersExtensionTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new HelpersExtension();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\HelpersExtension::getFilters
     */
    public function testGetFilters()
    {
        $this->assertNotEmpty($this->extension->getFilters());

        foreach ($this->extension->getFilters() as $Filter) {
            $this->assertInstanceOf(TwigFilter::class, $Filter);
        }
    }
}