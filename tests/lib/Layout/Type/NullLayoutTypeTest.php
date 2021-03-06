<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Type;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use PHPUnit\Framework\TestCase;

final class NullLayoutTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\NullLayoutType
     */
    private $layoutType;

    public function setUp(): void
    {
        $this->layoutType = new NullLayoutType('type');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::__construct
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('type', $this->layoutType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertTrue($this->layoutType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Invalid layout type', $this->layoutType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('', $this->layoutType->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getZones
     */
    public function testGetZones(): void
    {
        self::assertSame([], $this->layoutType->getZones());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getZoneIdentifiers
     */
    public function testGetZoneIdentifiers(): void
    {
        self::assertSame([], $this->layoutType->getZoneIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::hasZone
     */
    public function testHasZone(): void
    {
        self::assertFalse($this->layoutType->hasZone('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::getZone
     */
    public function testGetZone(): void
    {
        $this->expectException(LayoutTypeException::class);
        $this->expectExceptionMessage('Zone "left" does not exist in "type" layout type.');

        $this->layoutType->getZone('left');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Type\NullLayoutType::isBlockAllowedInZone
     */
    public function testIsBlockAllowedInZone(): void
    {
        self::assertTrue($this->layoutType->isBlockAllowedInZone(BlockDefinition::fromArray(['identifier' => 'title']), 'left'));
    }
}
