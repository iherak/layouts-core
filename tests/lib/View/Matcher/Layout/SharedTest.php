<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Layout\Shared;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class SharedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Shared();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Shared::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $layout = Layout::fromArray(
            [
                'shared' => true,
            ]
        );

        $view = new LayoutView($layout);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], true],
            [[true], true],
            [[false], false],
            [['something_else'], false],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Shared::match
     */
    public function testMatchWithNoLayoutView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
