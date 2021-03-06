<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\View\View;

final class BlockView extends View implements BlockViewInterface
{
    public function __construct(Block $block)
    {
        $this->parameters['block'] = $block;
    }

    public function getBlock(): Block
    {
        return $this->parameters['block'];
    }

    public static function getIdentifier(): string
    {
        return 'block';
    }
}
