<?php

namespace Netgen\BlockManager\Collection\Result;

use Iterator;
use IteratorIterator;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;

/**
 * This iterator builds a result object from the collection item.
 */
final class ResultBuilderIterator extends IteratorIterator
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(
        Iterator $iterator,
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder,
        VisibilityResolverInterface $visibilityResolver
    ) {
        parent::__construct($iterator);

        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
        $this->visibilityResolver = $visibilityResolver;
    }

    /**
     * Returns the result for provided object.
     *
     * Object can be a collection item, which only holds the reference
     * to the value (ID and type), or a value itself.
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    public function current()
    {
        $object = parent::current();
        $position = parent::key();

        if (!$object instanceof Item) {
            return new Result(
                array(
                    'item' => !$object instanceof ItemInterface ?
                        $this->itemBuilder->build($object) :
                        $object,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'position' => $position,
                    'isVisible' => true,
                )
            );
        }

        try {
            $item = $this->itemLoader->load(
                $object->getValue(),
                $object->getValueType()
            );
        } catch (ItemException $e) {
            $item = new NullItem(
                array(
                    'value' => $object->getValue(),
                )
            );
        }

        return new Result(
            array(
                'item' => $item,
                'collectionItem' => $object,
                'type' => Result::TYPE_MANUAL,
                'position' => $position,
                'isVisible' => $item->isVisible() && $this->visibilityResolver->isVisible($object),
            )
        );
    }
}
