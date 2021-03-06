<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Response;

final class RestoreFromArchive extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Restores the layout from archive to a draft.
     */
    public function __invoke(Layout $layout): Response
    {
        $this->layoutService->restoreFromArchive($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
