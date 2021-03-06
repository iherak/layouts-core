<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Response;

interface TaggerInterface
{
    /**
     * Tags the response with data from the provided layout.
     */
    public function tagLayout(Response $response, Layout $layout): void;

    /**
     * Tags the response with data from the provided block.
     */
    public function tagBlock(Response $response, Block $block): void;
}
