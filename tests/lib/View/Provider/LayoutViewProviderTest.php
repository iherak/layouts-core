<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\View\Provider\LayoutViewProvider;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use PHPUnit\Framework\TestCase;

final class LayoutViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $layoutViewProvider;

    public function setUp(): void
    {
        $this->layoutViewProvider = new LayoutViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $layout = Layout::fromArray(['id' => 42]);

        $view = $this->layoutViewProvider->provideView($layout);

        self::assertInstanceOf(LayoutViewInterface::class, $view);

        self::assertSame($layout, $view->getLayout());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'layout' => $layout,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->layoutViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new LayoutType(), false],
            [new Layout(), true],
        ];
    }
}
