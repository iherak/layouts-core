<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class LoadCollectionResult extends Controller
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    public function __construct(PagerFactory $pagerFactory)
    {
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * Returns the collection result.
     */
    public function __invoke(Block $block, string $collectionIdentifier): VersionedValue
    {
        $collection = $block->getCollection($collectionIdentifier);

        // In non AJAX scenarios, we're always rendering the first page of the collection
        // as specified by offset and limit in the collection itself
        $pager = $this->pagerFactory->getPager($collection, 1, null, ResultSet::INCLUDE_ALL_ITEMS);

        return new VersionedValue($pager->getCurrentPageResults(), Version::API_V1);
    }
}
