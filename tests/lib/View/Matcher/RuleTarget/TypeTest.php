<?php

namespace Netgen\BlockManager\Tests\View\Matcher\RuleTarget;

use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\RuleTarget\Type;
use Netgen\BlockManager\View\View\RuleTargetView;
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
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $target = new Target(
            [
                'targetType' => new TargetType('route'),
            ]
        );

        $view = new RuleTargetView(['target' => $target]);

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNullTargetType()
    {
        $target = new Target(
            [
                'targetType' => new NullTargetType('type'),
            ]
        );

        $view = new RuleTargetView(['target' => $target]);

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNullTargetTypeReturnsFalse()
    {
        $target = new Target(
            [
                'targetType' => new NullTargetType('type'),
            ]
        );

        $view = new RuleTargetView(['target' => $target]);

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return [
            [[], false],
            [['some_type'], false],
            [['route'], true],
            [['some_type', 'some_type_2'], false],
            [['some_type', 'route'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNoRuleTargetView()
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }
}
