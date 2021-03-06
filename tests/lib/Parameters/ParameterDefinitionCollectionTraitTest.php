<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ParameterDefinitionCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinition
     */
    public function testGetParameterDefinition(): void
    {
        $definition = new ParameterDefinition();

        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => $definition]
        );

        self::assertSame($definition, $parameterDefinitions->getParameterDefinition('name'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinition
     */
    public function testGetParameterDefinitionWithNonExistingDefinition(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Parameter definition with "test" name does not exist.');

        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()]
        );

        $parameterDefinitions->getParameterDefinition('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinitions
     */
    public function testGetParameterDefinitions(): void
    {
        $definition = new ParameterDefinition();

        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => $definition]
        );

        self::assertSame(
            ['name' => $definition],
            $parameterDefinitions->getParameterDefinitions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait::hasParameterDefinition
     */
    public function testHasParameterDefinition(): void
    {
        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()]
        );

        self::assertFalse($parameterDefinitions->hasParameterDefinition('test'));
        self::assertTrue($parameterDefinitions->hasParameterDefinition('name'));
    }
}
