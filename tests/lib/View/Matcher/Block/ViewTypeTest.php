<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\ViewType;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new ViewType();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $block = new Block(
            array(
                'viewType' => 'default',
            )
        );

        $view = new BlockView(
            array(
                'block' => $block,
            )
        );

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return array(
            array(array(), false),
            array(array('small'), false),
            array(array('default'), true),
            array(array('small', 'large'), false),
            array(array('small', 'default'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     */
    public function testMatchWithNoBlockView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
