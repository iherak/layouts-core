<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Copy extends Controller
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
     * Copies the layout.
     */
    public function __invoke(Layout $layout, Request $request): View
    {
        $requestData = $request->attributes->get('data');

        $copyStruct = $this->layoutService->newLayoutCopyStruct();
        $copyStruct->name = $requestData->get('name');
        $copyStruct->description = $requestData->get('description');

        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        return new View($copiedLayout, Version::API_V1, Response::HTTP_CREATED);
    }
}
