<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\Layout\LayoutInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Modified implements ColumnValueProviderInterface
{
    /**
     * @var string
     */
    private $dateFormat;

    public function __construct(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getValue(ItemInterface $item): ?string
    {
        if (!$item instanceof LayoutInterface) {
            return null;
        }

        return $item->getLayout()->getModified()->format(
            $this->dateFormat
        );
    }
}
