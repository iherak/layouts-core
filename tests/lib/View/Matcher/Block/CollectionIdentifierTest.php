<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class CollectionIdentifierTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new CollectionIdentifier();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new BlockView(new Block());

        $view->addParameter('collection_identifier', 'default');

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['featured'], false],
            [['default'], true],
            [['featured', 'featured2'], false],
            [['featured', 'default'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoBlockView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoCollectionIdentifier(): void
    {
        self::assertFalse($this->matcher->match(new BlockView(new Block()), []));
    }
}
