<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class BlockUpdateStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct
     */
    private $struct;

    public function setUp(): void
    {
        $this->struct = new BlockUpdateStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct::fillParametersFromBlock
     */
    public function testFillParametersFromBlock(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $blockDefinition->getParameterDefinition('compound');

        $block = Block::fromArray(
            [
                'definition' => $blockDefinition,
                'parameters' => [
                    'css_class' => Parameter::fromArray(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $blockDefinition->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => Parameter::fromArray(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillParametersFromBlock($block);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockUpdateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    private function buildBlockDefinition(): BlockDefinitionInterface
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        ]
                    ),
                ],
            ]
        );

        $parameterDefinitions = [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return BlockDefinition::fromArray(['parameterDefinitions' => $parameterDefinitions]);
    }
}
