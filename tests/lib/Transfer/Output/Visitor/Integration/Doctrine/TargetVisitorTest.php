<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\TargetVisitorTest as BaseTargetVisitorTest;

/**
 * @covers \Netgen\BlockManager\Transfer\Output\Visitor\TargetVisitor
 */
final class TargetVisitorTest extends BaseTargetVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
