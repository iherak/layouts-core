<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor\ZoneVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class ZoneVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

        $this->getVisitor()->visit(new Zone());
    }

    public function getVisitor(): VisitorInterface
    {
        return new ZoneVisitor($this->blockService);
    }

    public function acceptProvider(): array
    {
        return [
            [new Zone(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Zone { return $this->layoutService->loadZone(2, 'top'); }, 'zone/zone_2_top.json'],
            [function (): Zone { return $this->layoutService->loadZone(2, 'right'); }, 'zone/zone_2_right.json'],
            [function (): Zone { return $this->layoutService->loadZone(6, 'bottom'); }, 'zone/zone_6_bottom.json'],
        ];
    }
}
