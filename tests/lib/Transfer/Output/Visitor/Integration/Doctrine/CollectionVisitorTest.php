<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\CollectionVisitorTest as BaseCollectionVisitorTest;

/**
 * @covers \Netgen\BlockManager\Transfer\Output\Visitor\CollectionVisitor
 */
final class CollectionVisitorTest extends BaseCollectionVisitorTest
{
    use TestCaseTrait;

    public function tearDown(): void
    {
        $this->closeDatabase();
    }
}
