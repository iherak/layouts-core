<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use PHPUnit\Framework\TestCase;

final class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertFalse($layoutCreateStruct->shared);
    }
}
