<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockType;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    private $blockTypeGroup;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType1;

    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType
     */
    private $blockType2;

    public function setUp(): void
    {
        $this->blockType1 = BlockType::fromArray(['isEnabled' => true, 'identifier' => 'type']);
        $this->blockType2 = BlockType::fromArray(['isEnabled' => false, 'identifier' => 'type2']);

        $this->blockTypeGroup = BlockTypeGroup::fromArray(
            [
                'identifier' => 'simple_blocks',
                'isEnabled' => false,
                'name' => 'Simple blocks',
                'blockTypes' => [$this->blockType1, $this->blockType2],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('simple_blocks', $this->blockTypeGroup->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertFalse($this->blockTypeGroup->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Simple blocks', $this->blockTypeGroup->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetBlockTypes(): void
    {
        self::assertSame([$this->blockType1, $this->blockType2], $this->blockTypeGroup->getBlockTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockType\BlockTypeGroup::getBlockTypes
     */
    public function testGetEnabledBlockTypes(): void
    {
        self::assertSame([$this->blockType1], $this->blockTypeGroup->getBlockTypes(true));
    }
}
