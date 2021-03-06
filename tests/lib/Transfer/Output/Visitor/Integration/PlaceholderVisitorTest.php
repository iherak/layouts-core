<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor\PlaceholderVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class PlaceholderVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

        $this->getVisitor()->visit(new Placeholder());
    }

    public function getVisitor(): VisitorInterface
    {
        return new PlaceholderVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Placeholder(), true],
            [new Layout(), false],
            [new Collection(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Placeholder { return $this->blockService->loadBlock(33)->getPlaceholder('left'); }, 'placeholder/block_33_left.json'],
            [function (): Placeholder { return $this->blockService->loadBlock(33)->getPlaceholder('right'); }, 'placeholder/block_33_right.json'],
        ];
    }
}
