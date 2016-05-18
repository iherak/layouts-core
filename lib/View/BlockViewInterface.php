<?php

namespace Netgen\BlockManager\View;

interface BlockViewInterface extends ViewInterface
{
    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock();
}
