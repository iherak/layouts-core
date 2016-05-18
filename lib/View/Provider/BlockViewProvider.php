<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;

class BlockViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView(Value $value)
    {
        /** @var \Netgen\BlockManager\API\Values\Page\Block $value */
        $blockView = new BlockView($value);

        return $blockView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports(Value $value)
    {
        return $value instanceof Block;
    }
}
