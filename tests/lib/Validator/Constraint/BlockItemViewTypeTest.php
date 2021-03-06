<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;
use PHPUnit\Framework\TestCase;

final class BlockItemViewTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\BlockItemViewType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockItemViewType();
        self::assertSame('ngbm_block_item_view_type', $constraint->validatedBy());
    }
}
