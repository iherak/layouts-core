<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    private $struct;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct
     */
    private $collectionStruct;

    public function setUp(): void
    {
        $this->collectionStruct = new CollectionCreateStruct(['offset' => 0]);

        $this->struct = new BlockCreateStruct(
            [
                'collectionCreateStructs' => [
                    'default' => $this->collectionStruct,
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::getCollectionCreateStructs
     */
    public function testGetCollectionCreateStructs(): void
    {
        $this->assertSame(
            ['default' => $this->collectionStruct],
            $this->struct->getCollectionCreateStructs()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::addCollectionCreateStruct
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::getCollectionCreateStructs
     */
    public function testAddCollectionCreateStruct(): void
    {
        $collectionStruct1 = new CollectionCreateStruct(['offset' => 5]);
        $collectionStruct2 = new CollectionCreateStruct(['offset' => 10]);

        $this->struct->addCollectionCreateStruct('default', $collectionStruct1);
        $this->struct->addCollectionCreateStruct('featured', $collectionStruct2);

        $this->assertSame(
            [
                'default' => $collectionStruct1,
                'featured' => $collectionStruct2,
            ],
            $this->struct->getCollectionCreateStructs()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillDefaultParameters
     */
    public function testFillDefaultParameters(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        $this->struct->fillDefaultParameters($blockDefinition);

        $this->assertSame(
            [
                'css_class' => 'css_default',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner_default',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParametersFromBlock
     */
    public function testFillParametersFromBlock(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundDefinition */
        $compoundDefinition = $blockDefinition->getParameterDefinition('compound');

        $block = new Block(
            [
                'definition' => $blockDefinition,
                'parameters' => [
                    'css_class' => new Parameter(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $blockDefinition->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => new Parameter(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundDefinition->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillParametersFromBlock($block);

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParametersFromHash
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

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues(): void
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

        $this->assertSame(
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
        $compoundParameter = new CompoundParameterDefinition(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => new ParameterDefinition(
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
            'css_class' => new ParameterDefinition(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => new ParameterDefinition(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return new BlockDefinition(['parameterDefinitions' => $parameterDefinitions]);
    }
}
