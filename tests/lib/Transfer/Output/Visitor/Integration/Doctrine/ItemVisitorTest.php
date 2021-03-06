<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\ItemVisitorTest as BaseItemVisitorTest;

/**
 * @covers \Netgen\BlockManager\Transfer\Output\Visitor\ItemVisitor
 */
final class ItemVisitorTest extends BaseItemVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
