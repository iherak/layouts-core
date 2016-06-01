<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\BlockViewType;
use Netgen\BlockManager\Validator\Constraint\Parameters;
use Symfony\Component\Validator\Constraints;

class BlockValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(BlockDefinitionRegistryInterface $blockDefinitionRegistry)
    {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Validates block create struct.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $blockCreateStruct->definitionIdentifier
        );

        $this->validate(
            $blockCreateStruct->definitionIdentifier,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
            ),
            'definitionIdentifier'
        );

        $this->validate(
            $blockCreateStruct->viewType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Type(array('type' => 'string')),
                new BlockViewType(array('definition' => $blockDefinition)),
            ),
            'viewType'
        );

        if ($blockCreateStruct->name !== null) {
            $this->validate(
                $blockCreateStruct->name,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        $this->validate(
            $blockCreateStruct->getParameters(),
            array(
                new Parameters(
                    array(
                        'parameters' => $blockDefinition->getParameters(),
                        'required' => true,
                    )
                ),
            ),
            'parameters'
        );

        return true;
    }

    /**
     * Validates block update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the validation failed
     *
     * @return bool
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($block->getDefinitionIdentifier());

        if ($blockUpdateStruct->viewType !== null) {
            $this->validate(
                $blockUpdateStruct->viewType,
                array(
                    new Constraints\Type(array('type' => 'string')),
                    new BlockViewType(array('definition' => $blockDefinition)),
                ),
                'viewType'
            );
        }

        if ($blockUpdateStruct->name !== null) {
            $this->validate(
                $blockUpdateStruct->name,
                array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'name'
            );
        }

        $this->validate(
            $blockUpdateStruct->getParameters(),
            array(
                new Parameters(
                    array(
                        'parameters' => $blockDefinition->getParameters(),
                        'required' => false,
                    )
                ),
            ),
            'parameters'
        );

        return true;
    }
}
