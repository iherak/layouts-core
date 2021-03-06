<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\PlaceholderVisitorTest as BasePlaceholderVisitorTest;

/**
 * @covers \Netgen\BlockManager\Transfer\Output\Visitor\PlaceholderVisitor
 */
final class PlaceholderVisitorTest extends BasePlaceholderVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
