<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\View\Provider\PlaceholderViewProvider;
use Netgen\BlockManager\View\View\PlaceholderViewInterface;
use PHPUnit\Framework\TestCase;

final class PlaceholderViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $placeholderViewProvider;

    public function setUp(): void
    {
        $this->placeholderViewProvider = new PlaceholderViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $block = new Block();
        $placeholder = new Placeholder();

        $view = $this->placeholderViewProvider->provideView(
            $placeholder,
            [
                'block' => $block,
            ]
        );

        self::assertInstanceOf(PlaceholderViewInterface::class, $view);

        self::assertSame($placeholder, $view->getPlaceholder());
        self::assertSame($block, $view->getBlock());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'placeholder' => $placeholder,
                'block' => $block,
            ],
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideViewThrowsRuntimeExceptionOnMissingBlock(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the placeholder view, "block" parameter needs to be provided.');

        $this->placeholderViewProvider->provideView(new Placeholder());
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideViewThrowsRuntimeExceptionOnInvalidBlock(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the placeholder view, "block" parameter needs to be of "Netgen\\BlockManager\\API\\Values\\Block\\Block" type.');

        $this->placeholderViewProvider->provideView(new Placeholder(), ['block' => 42]);
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\PlaceholderViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->placeholderViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Placeholder(), true],
            [new Block(), false],
        ];
    }
}
