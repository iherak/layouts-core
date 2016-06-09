<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $block = new Block();

        self::assertNull($block->id);
        self::assertNull($block->layoutId);
        self::assertNull($block->zoneIdentifier);
        self::assertNull($block->position);
        self::assertNull($block->definitionIdentifier);
        self::assertNull($block->parameters);
        self::assertNull($block->viewType);
        self::assertNull($block->itemViewType);
        self::assertNull($block->name);
    }

    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 84,
                'zoneIdentifier' => 'top',
                'position' => 4,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        self::assertEquals(42, $block->id);
        self::assertEquals(84, $block->layoutId);
        self::assertEquals('top', $block->zoneIdentifier);
        self::assertEquals(4, $block->position);
        self::assertEquals('paragraph', $block->definitionIdentifier);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->parameters
        );
        self::assertEquals('default', $block->viewType);
        self::assertEquals('standard', $block->itemViewType);
        self::assertEquals('My block', $block->name);
    }
}
