<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\PlaceholderDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

class BlockDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $handlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterBuilderMock;

    public function setUp()
    {
        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);
        $this->configMock = $this->createMock(Configuration::class);
        $this->parameterBuilderMock = $this->createMock(ParameterBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     */
    public function testBuildBlockDefinition()
    {
        $this->handlerMock
            ->expects($this->any())
            ->method('getPlaceholderIdentifiers')
            ->will($this->returnValue(array()));

        $blockDefinition = BlockDefinitionFactory::buildBlockDefinition(
            'definition',
            $this->handlerMock,
            $this->configMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $blockDefinition->getConfig());
        $this->assertFalse($blockDefinition->isContainer());

        $this->assertCount(0, $blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     */
    public function testBuildContainerBlockDefinition()
    {
        $this->handlerMock
            ->expects($this->any())
            ->method('getPlaceholderIdentifiers')
            ->will($this->returnValue(array('left', 'right')));

        $this->handlerMock
            ->expects($this->any())
            ->method('isContainer')
            ->will($this->returnValue(true));

        $blockDefinition = BlockDefinitionFactory::buildBlockDefinition(
            'definition',
            $this->handlerMock,
            $this->configMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $blockDefinition->getConfig());
        $this->assertTrue($blockDefinition->isContainer());

        $this->assertCount(2, $blockDefinition->getPlaceholders());
        foreach ($blockDefinition->getPlaceholders() as $placeholder) {
            $this->assertInstanceOf(PlaceholderDefinitionInterface::class, $placeholder);
        }
    }
}
