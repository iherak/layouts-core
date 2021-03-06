<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Item\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Item implements ItemInterface, LayoutInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getValue()
    {
        return $this->layout->getId();
    }

    public function getName(): string
    {
        return $this->layout->getName();
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }
}
