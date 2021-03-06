<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\StructBuilder;

use Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder;
use Netgen\BlockManager\Core\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class BlockStructBuilderTest extends CoreTestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new BlockStructBuilder(new ConfigStructBuilder());
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder::__construct
     * @covers \Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct(): void
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $struct = $this->structBuilder->newBlockCreateStruct($blockDefinition);

        self::assertSame(
            [
                'viewType' => 'small',
                'itemViewType' => 'standard',
                'name' => null,
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'definition' => $blockDefinition,
                'collectionCreateStructs' => [],
                'parameterValues' => [
                    'css_class' => 'some-class',
                    'css_id' => null,
                ],
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct(): void
    {
        $struct = $this->structBuilder->newBlockUpdateStruct('en');

        self::assertSame(
            [
                'locale' => 'en',
                'viewType' => null,
                'itemViewType' => null,
                'name' => null,
                'alwaysAvailable' => null,
                'parameterValues' => [],
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\StructBuilder\BlockStructBuilder::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(36);
        $struct = $this->structBuilder->newBlockUpdateStruct('en', $block);

        self::assertArrayHasKey('key', $struct->getConfigStructs());

        self::assertSame(
            [
                'locale' => 'en',
                'viewType' => 'title',
                'itemViewType' => 'standard',
                'name' => 'My sixth block',
                'alwaysAvailable' => true,
                'parameterValues' => [
                    'css_class' => 'CSS class',
                    'css_id' => null,
                ],
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
            ],
            $this->exportObject($struct, true)
        );
    }
}
