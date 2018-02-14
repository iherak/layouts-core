<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\TextType;
use PHPUnit\Framework\TestCase;

final class ParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getType
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getLabel
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     */
    public function testSetDefaultProperties()
    {
        $parameterDefinition = new ParameterDefinition();

        $this->assertNull($parameterDefinition->getName());
        $this->assertNull($parameterDefinition->getType());
        $this->assertNull($parameterDefinition->getOptions());
        $this->assertNull($parameterDefinition->isRequired());
        $this->assertNull($parameterDefinition->getDefaultValue());
        $this->assertNull($parameterDefinition->getLabel());
        $this->assertNull($parameterDefinition->getGroups());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getName
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getType
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::hasOption
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getOption
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::isRequired
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getDefaultValue
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getLabel
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition::getGroups
     */
    public function testSetProperties()
    {
        $parameterDefinition = new ParameterDefinition(
            array(
                'name' => 'name',
                'type' => new TextType(),
                'options' => array('option' => 'value'),
                'isRequired' => true,
                'defaultValue' => 42,
                'label' => 'Custom label',
                'groups' => array('group'),
            )
        );

        $this->assertEquals('name', $parameterDefinition->getName());
        $this->assertEquals(new TextType(), $parameterDefinition->getType());
        $this->assertEquals(array('option' => 'value'), $parameterDefinition->getOptions());
        $this->assertTrue($parameterDefinition->hasOption('option'));
        $this->assertFalse($parameterDefinition->hasOption('other'));
        $this->assertEquals('value', $parameterDefinition->getOption('option'));
        $this->assertTrue($parameterDefinition->isRequired());
        $this->assertEquals(42, $parameterDefinition->getDefaultValue());
        $this->assertEquals('Custom label', $parameterDefinition->getLabel());
        $this->assertEquals(array('group'), $parameterDefinition->getGroups());

        try {
            $parameterDefinition->getOption('other');

            $this->fail('Non existing option was returned.');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }
}