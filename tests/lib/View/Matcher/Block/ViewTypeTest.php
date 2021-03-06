<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\ViewType;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new ViewType();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $block = Block::fromArray(
            [
                'viewType' => 'default',
            ]
        );

        $view = new BlockView($block);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['small'], false],
            [['default'], true],
            [['small', 'large'], false],
            [['small', 'default'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     */
    public function testMatchWithNoBlockView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
