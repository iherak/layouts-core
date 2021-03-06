<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Item\Layout;

use Netgen\ContentBrowser\Item\LocationInterface;

final class RootLocation implements LocationInterface
{
    public function getLocationId()
    {
        return 0;
    }

    public function getName(): string
    {
        return 'All layouts';
    }

    public function getParentId()
    {
        return null;
    }
}
