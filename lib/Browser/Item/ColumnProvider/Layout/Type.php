<?php

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

class Type implements ColumnValueProviderInterface
{
    /**
     * Provides the column value.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return mixed
     */
    public function getValue(ItemInterface $item)
    {
        return $item->getLayout()->getLayoutType()->getName();
    }
}