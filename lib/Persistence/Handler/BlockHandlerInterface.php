<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct;
use Netgen\BlockManager\Persistence\Values\Block\TranslationUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;

interface BlockHandlerInterface
{
    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function loadBlock($blockId, $status);

    /**
     * Returns if block with specified ID exists.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return bool
     */
    public function blockExists($blockId, $status);

    /**
     * Loads all blocks from specified layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadLayoutBlocks(Layout $layout);

    /**
     * Loads all blocks from specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadZoneBlocks(Zone $zone);

    /**
     * Loads all blocks from specified block, optionally filtered by placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $placeholder
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block[]
     */
    public function loadChildBlocks(Block $block, $placeholder = null);

    /**
     * Loads a collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection reference with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
    public function loadCollectionReference(Block $block, $identifier);

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference[]
     */
    public function loadCollectionReferences(Block $block);

    /**
     * Creates a block in specified target block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *                                                          If target block does not belong to layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, Layout $layout, Block $targetBlock = null, $placeholder = null);

    /**
     * Creates a block translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $locale
     * @param string $sourceLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If translation with provided locale already exists
     *                                                          If translation with provided source locale does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlockTranslation(Block $block, $locale, $sourceLocale);

    /**
     * Creates the collection reference.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\CollectionReferenceCreateStruct $createStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\CollectionReference
     */
    public function createCollectionReference(Block $block, CollectionReferenceCreateStruct $createStruct);

    /**
     * Updates a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function updateBlock(Block $block, BlockUpdateStruct $blockUpdateStruct);

    /**
     * Updates a block translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $locale
     * @param \Netgen\BlockManager\Persistence\Values\Block\TranslationUpdateStruct $translationUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If the block does not have the provided locale
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function updateBlockTranslation(Block $block, $locale, TranslationUpdateStruct $translationUpdateStruct);

    /**
     * Updates the main translation of the block.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $mainLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided locale does not exist in the block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function setMainTranslation(Block $block, $mainLocale);

    /**
     * Copies a block to a specified target block and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is within the provided block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function copyBlock(Block $block, Block $targetBlock, $placeholder, $position = null);

    /**
     * Moves a block to specified position in a specified target block and placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $targetBlock
     * @param string $placeholder
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     * @throws \Netgen\BlockManager\Exception\BadStateException If block is already in target block and placeholder
     * @throws \Netgen\BlockManager\Exception\BadStateException If target block is within the provided block
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function moveBlock(Block $block, Block $targetBlock, $placeholder, $position);

    /**
     * Moves a block to specified position in the current placeholder.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function moveBlockToPosition(Block $block, $position);

    /**
     * Creates a new block status.
     *
     * This method does not create new status for sub-blocks,
     * so any process that works with this method needs to take care of that.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function createBlockStatus(Block $block, $newStatus);

    /**
     * Restores all block data (except placement and position) from the specified status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param int $fromStatus
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if block is already in provided status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function restoreBlock(Block $block, $fromStatus);

    /**
     * Deletes a block with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     */
    public function deleteBlock(Block $block);

    /**
     * Deletes provided block translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Block\Block $block
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If translation with provided locale does not exist
     *                                                          If translation with provided locale is the main block translation
     *
     * @return \Netgen\BlockManager\Persistence\Values\Block\Block
     */
    public function deleteBlockTranslation(Block $block, $locale);

    /**
     * Deletes all blocks belonging to specified layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutBlocks($layoutId, $status = null);

    /**
     * Deletes provided blocks.
     *
     * This is an internal method that only deletes the blocks with provided IDs.
     *
     * If you want to delete a block and all of its sub-blocks, use self::deleteBlock method.
     *
     * @param array $blockIds
     * @param int $status
     */
    public function deleteBlocks(array $blockIds, $status = null);
}
