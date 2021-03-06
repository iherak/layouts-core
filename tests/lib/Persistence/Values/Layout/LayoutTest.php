<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class LayoutTest extends TestCase
{
    public function testSetProperties(): void
    {
        $layout = Layout::fromArray(
            [
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My description',
                'shared' => true,
                'created' => 123,
                'modified' => 456,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $layout->id);
        self::assertSame('4_zones_a', $layout->type);
        self::assertSame('My layout', $layout->name);
        self::assertSame('My description', $layout->description);
        self::assertTrue($layout->shared);
        self::assertSame(123, $layout->created);
        self::assertSame(456, $layout->modified);
        self::assertSame(Value::STATUS_PUBLISHED, $layout->status);
    }
}
