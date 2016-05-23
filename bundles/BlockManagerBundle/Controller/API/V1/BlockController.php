<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\EditView;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use RuntimeException;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator $validator
     */
    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        CollectionService $collectionService,
        BlockValidator $validator
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->collectionService = $collectionService;
        $this->validator = $validator;
    }

    /**
     * Loads a block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(Block $block)
    {
        return new View($block, Version::API_V1);
    }

    /**
     * Loads all block collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollections(Block $block)
    {
        $collections = array_map(
            function (CollectionReference $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->blockService->loadCollectionReferences($block)
        );

        return new ValueArray($collections);
    }

    /**
     * Creates the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type or block definition does not exist
     *                                                              If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $this->validator->validateCreateBlock($request);

        try {
            $blockType = $this->getBlockType($request->request->get('block_type'));
        } catch (RuntimeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        try {
            $this->getBlockDefinition($blockType->getDefinitionIdentifier());
        } catch (RuntimeException $e) {
            throw new BadStateException('block_type', 'Block definition specified in block type does not exist.', $e);
        }

        try {
            $layout = $this->layoutService->loadLayout(
                $request->request->get('layout_id'),
                Layout::STATUS_DRAFT
            );
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout does not exist.', $e);
        }

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $blockType->getDefinitionIdentifier(),
            $blockType->getDefaultBlockViewType()
        );

        $blockCreateStruct->name = $blockType->getDefaultBlockName();
        $blockCreateStruct->setParameters($blockType->getDefaultBlockParameters());

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $layout,
            $request->request->get('zone_identifier'),
            $request->request->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Displays block edit interface.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\EditView
     */
    public function edit(Block $block)
    {
        $defaultCollection = null;
        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            if ($collectionReference->getIdentifier() === 'default') {
                $defaultCollection = $this->collectionService->loadCollection(
                    $collectionReference->getCollectionId(),
                    $collectionReference->getCollectionStatus()
                );
            }
        }

        $editView = new EditView($block, Version::API_V1);
        $editView->setViewParameters(
            array(
                'block_definition' => $this->getBlockDefinition($block->getDefinitionIdentifier()),
                'collection' => $defaultCollection,
                'named_collections' => $this->collectionService->loadNamedCollections(),
            )
        );

        return $editView;
    }

    /**
     * Moves the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move(Block $block, Request $request)
    {
        $this->blockService->moveBlock(
            $block,
            $request->request->get('position'),
            $request->request->get('zone_identifier')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Displays and processes block edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If form was not submitted
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function form(Block $block, $formName, Request $request)
    {
        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        if (!$blockDefinition->getConfig()->hasForm($formName)) {
            throw new InvalidArgumentException('form', 'Block does not support specified form.');
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockDefinition->getConfig()->getForm($formName)->getType(),
            $updateStruct,
            array('blockDefinition' => $blockDefinition)
        );

        $form->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            if (!$form->isSubmitted()) {
                throw new InvalidArgumentException('form', 'Form is not submitted.');
            }

            if (!$form->isValid()) {
                return new FormView($form, $block, Version::API_V1, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

            return new View($updatedBlock, Version::API_V1);
        }

        return new FormView($form, $block, Version::API_V1);
    }

    /**
     * Deletes the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Block $block)
    {
        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
