<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

final class NullBlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\NullBlockDefinition
     */
    private $blockDefinition;

    public function setUp(): void
    {
        $this->blockDefinition = new NullBlockDefinition('definition');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::__construct
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Invalid block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isTranslatable
     */
    public function testIsTranslatable(): void
    {
        self::assertFalse($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getForms
     */
    public function testGetForms(): void
    {
        self::assertSame([], $this->blockDefinition->getForms());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasForm
     */
    public function testHasForm(): void
    {
        self::assertFalse($this->blockDefinition->hasForm('content'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getForm
     */
    public function testGetForm(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Form "content" does not exist in "definition" block definition.');

        $this->blockDefinition->getForm('content');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        self::assertSame([], $this->blockDefinition->getCollections());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasCollection
     */
    public function testHasCollection(): void
    {
        self::assertFalse($this->blockDefinition->hasCollection('collection'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollection
     */
    public function testGetCollection(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Collection "collection" does not exist in "definition" block definition.');

        $this->blockDefinition->getCollection('collection');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        self::assertSame([], $this->blockDefinition->getViewTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers(): void
    {
        self::assertSame([], $this->blockDefinition->getViewTypeIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasViewType
     */
    public function testHasViewType(): void
    {
        self::assertFalse($this->blockDefinition->hasViewType('large'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewType
     */
    public function testGetViewType(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('View type "large" does not exist in "definition" block definition.');

        $this->blockDefinition->getViewType('large');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }
}
