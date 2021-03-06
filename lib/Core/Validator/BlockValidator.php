<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Validator;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct as BlockCreateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;

final class BlockValidator extends Validator
{
    /**
     * @var \Netgen\BlockManager\Core\Validator\CollectionValidator
     */
    private $collectionValidator;

    public function __construct(CollectionValidator $collectionValidator)
    {
        $this->collectionValidator = $collectionValidator;
    }

    /**
     * Validates the provided block create struct.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateBlockCreateStruct(BlockCreateStruct $blockCreateStruct): void
    {
        $this->validate(
            $blockCreateStruct,
            [
                new BlockCreateStructConstraint(),
            ]
        );

        $this->validate(
            $blockCreateStruct,
            new ConfigAwareStructConstraint(
                [
                    'payload' => $blockCreateStruct->getDefinition(),
                ]
            )
        );

        $collectionCreateStructs = $blockCreateStruct->getCollectionCreateStructs();
        if (!empty($collectionCreateStructs)) {
            foreach ($collectionCreateStructs as $collectionCreateStruct) {
                $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);
            }
        }
    }

    /**
     * Validates the provided block update struct.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateBlockUpdateStruct(Block $block, BlockUpdateStruct $blockUpdateStruct): void
    {
        $this->validate(
            $blockUpdateStruct,
            [
                new BlockUpdateStructConstraint(
                    [
                        'payload' => $block,
                    ]
                ),
            ]
        );

        $this->validate(
            $blockUpdateStruct,
            new ConfigAwareStructConstraint(
                [
                    'payload' => $block->getDefinition(),
                    'allowMissingFields' => true,
                ]
            )
        );
    }
}
