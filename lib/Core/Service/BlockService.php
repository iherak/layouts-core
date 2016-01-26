<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\API\Service\Validator\BlockValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Exception\BadStateException;
use Exception;

class BlockService implements BlockServiceInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\Validator\BlockValidator
     */
    protected $blockValidator;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Validator\BlockValidator $blockValidator
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(BlockValidator $blockValidator, Handler $persistenceHandler)
    {
        $this->blockValidator = $blockValidator;
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If block ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlock($blockId, $status = APILayout::STATUS_PUBLISHED)
    {
        if (!is_int($blockId) && !is_string($blockId)) {
            throw new InvalidArgumentException('blockId', 'Value must be an integer or a string.');
        }

        if (empty($blockId)) {
            throw new InvalidArgumentException('blockId', 'Value must not be empty.');
        }

        return $this->buildDomainBlockObject(
            $this->persistenceHandler->getBlockHandler()->loadBlock($blockId, $status)
        );
    }

    /**
     * Loads blocks belonging to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function loadZoneBlocks(APIZone $zone, $status = APILayout::STATUS_PUBLISHED)
    {
        $persistenceBlocks = $this->persistenceHandler->getBlockHandler()->loadZoneBlocks($zone->getId(), $status);

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            $blocks[] = $this->buildDomainBlockObject($persistenceBlock);
        }

        return $blocks;
    }

    /**
     * Loads blocks belonging to specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function loadLayoutBlocks(APILayout $layout, $status = APILayout::STATUS_PUBLISHED)
    {
        $blocks = array();

        foreach ($layout->getZones() as $zone) {
            $zoneBlocks = $this->loadZoneBlocks($zone, $status);
            $blocks[$zone->getIdentifier()] = $zoneBlocks;
        }

        return $blocks;
    }

    /**
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, APIZone $zone)
    {
        if ($zone->getStatus() !== APILayout::STATUS_DRAFT) {
            throw new BadStateException('zone', 'Blocks can only be created in draft zones.');
        }

        if ($blockCreateStruct->name === null) {
            $blockCreateStruct->name = '';
        }

        $this->blockValidator->validateBlockCreateStruct($blockCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdBlock = $this->persistenceHandler->getBlockHandler()->createBlock($blockCreateStruct, $zone->getId());
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->buildDomainBlockObject($createdBlock);
    }

    /**
     * Updates a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If block is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function updateBlock(APIBlock $block, APIBlockUpdateStruct $blockUpdateStruct)
    {
        if ($block->getStatus() !== APILayout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be updated.');
        }

        if ($blockUpdateStruct->viewType === null) {
            $blockUpdateStruct->viewType = $block->getViewType();
        }

        if ($blockUpdateStruct->name === null) {
            $blockUpdateStruct->name = $block->getName();
        }

        // Merging the existing parameter array and those to be updated.
        // Excess parameters should be handled by validation.
        $blockUpdateStruct->setParameters(
            $blockUpdateStruct->getParameters() + $block->getParameters()
        );

        $this->blockValidator->validateBlockUpdateStruct($block, $blockUpdateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedBlock = $this->persistenceHandler->getBlockHandler()->updateBlock($block->getId(), $blockUpdateStruct);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->buildDomainBlockObject($updatedBlock);
    }

    /**
     * Copies a specified block. If zone is specified, copied block will be
     * placed in it, otherwise, it will be placed in the same zone where source block is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If specified zone is in a different layout
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If block or zone are not in a draft status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(APIBlock $block, APIZone $zone = null)
    {
        if ($zone instanceof APIZone) {
            $originalZone = $this->persistenceHandler->getLayoutHandler()->loadZone($block->getZoneId());
            if ($zone->getLayoutId() !== $originalZone->layoutId) {
                throw new InvalidArgumentException(
                    'zone->layoutId',
                    'Block cannot be copied to a different layout.'
                );
            }

            if ($zone->getStatus() !== APILayout::STATUS_DRAFT) {
                throw new BadStateException('zone', 'Blocks can only be copied to a zone in draft status.');
            }
        }

        if ($block->getStatus() !== APILayout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be copied.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedBlock = $this->persistenceHandler->getBlockHandler()->copyBlock(
                $block->getId(),
                $zone instanceof APIZone ? $zone->getId() : $block->getZoneId()
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->buildDomainBlockObject($copiedBlock);
    }

    /**
     * Moves a block to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If specified zone is in a different layout
     *                                                                     If target zone is the same as current zone
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If block or zone are not in a draft status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(APIBlock $block, APIZone $zone)
    {
        $originalZone = $this->persistenceHandler->getLayoutHandler()->loadZone($block->getZoneId());
        if ($zone->getLayoutId() !== $originalZone->layoutId) {
            throw new InvalidArgumentException(
                'zone->layoutId',
                'Block cannot be moved to a different layout.'
            );
        }

        if ($block->getZoneId() === $zone->getId()) {
            throw new InvalidArgumentException(
                'zone->id',
                'Block is already in specified zone.'
            );
        }

        if ($zone->getStatus() !== APILayout::STATUS_DRAFT) {
            throw new BadStateException('zone', 'Blocks can only be moved to a zone in draft status.');
        }

        if ($block->getStatus() !== APILayout::STATUS_DRAFT) {
            throw new BadStateException('block', 'Only blocks in draft status can be moved.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $movedBlock = $this->persistenceHandler->getBlockHandler()->moveBlock($block->getId(), $zone->getId());
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->buildDomainBlockObject($movedBlock);
    }

    /**
     * Deletes a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param int $status
     */
    public function deleteBlock(APIBlock $block, $status = null)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $this->persistenceHandler->getBlockHandler()->deleteBlock($block->getId(), $status);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new block create struct.
     *
     * @param string $definitionIdentifier
     * @param string $viewType
     *
     * @return \Netgen\BlockManager\API\Values\BlockCreateStruct
     */
    public function newBlockCreateStruct($definitionIdentifier, $viewType)
    {
        return new BlockCreateStruct(
            array(
                'definitionIdentifier' => $definitionIdentifier,
                'viewType' => $viewType,
            )
        );
    }

    /**
     * Creates a new block update struct.
     *
     * @return \Netgen\BlockManager\API\Values\BlockUpdateStruct
     */
    public function newBlockUpdateStruct()
    {
        return new BlockUpdateStruct();
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $persistenceBlock
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    protected function buildDomainBlockObject(PersistenceBlock $persistenceBlock)
    {
        $blockData = array(
            'id' => $persistenceBlock->id,
            'zoneId' => $persistenceBlock->zoneId,
            'definitionIdentifier' => $persistenceBlock->definitionIdentifier,
            'parameters' => $persistenceBlock->parameters,
            'viewType' => $persistenceBlock->viewType,
            'name' => $persistenceBlock->name,
            'status' => $persistenceBlock->status,
        );

        return new Block($blockData);
    }
}
