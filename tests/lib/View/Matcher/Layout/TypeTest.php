<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Layout\Type;
use Netgen\BlockManager\View\View\LayoutTypeView;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new Type();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $layout = new Layout(
            array(
                'layoutType' => new LayoutType(array('identifier' => '4_zones_a')),
            )
        );

        $view = new LayoutView(array('layout' => $layout));

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     * @dataProvider matchLayoutTypeProvider
     */
    public function testMatchLayoutType(array $config, $expected)
    {
        $view = new LayoutTypeView(array('layoutType' => new LayoutType(array('identifier' => '4_zones_a'))));

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
            array(array('some_type'), false),
            array(array('4_zones_a'), true),
            array(array('some_type', 'some_type_2'), false),
            array(array('some_type', '4_zones_a'), true),
        );
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchLayoutTypeProvider()
    {
        return array(
            array(array(), false),
            array(array('some_type'), false),
            array(array('4_zones_a'), true),
            array(array('some_type', 'some_type_2'), false),
            array(array('some_type', '4_zones_a'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNoLayoutOrLayoutTypeView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
