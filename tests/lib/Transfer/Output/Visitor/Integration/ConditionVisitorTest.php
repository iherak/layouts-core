<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Transfer\Output\Visitor\ConditionVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class ConditionVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    public function getVisitor(): VisitorInterface
    {
        return new ConditionVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Condition(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APICondition { return $this->layoutResolverService->loadCondition(1); }, 'condition/condition_1.json'],
            [function (): APICondition { return $this->layoutResolverService->loadCondition(2); }, 'condition/condition_2.json'],
        ];
    }
}