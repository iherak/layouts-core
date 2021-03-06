<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Netgen\BlockManager\Serializer\Values\View|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, string $locale, string $formName, Request $request)
    {
        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);

        $form = $this->createForm(
            $block->getDefinition()->getForm($formName)->getType(),
            $updateStruct,
            [
                'block' => $block,
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_edit',
                    [
                        'blockId' => $block->getId(),
                        'locale' => $locale,
                        'formName' => $formName,
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $block = $this->blockService->updateBlock($block, $form->getData());

            return new View($block, Version::API_V1);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
