<?php

namespace Netgen\BlockManager\API\Values\Collection;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\Value;

interface Item extends Value, ConfigAwareValue
{
    /**
     * Item of this type is inserted between items coming from the collection query.
     *
     * @const int
     */
    const TYPE_MANUAL = 0;

    /**
     * Items of this type override the item from the query at the specified position.
     *
     * @const int
     */
    const TYPE_OVERRIDE = 1;

    /**
     * Denotes that the item is visible. Does not take into account the possibility that
     * the CMS entity wrapped by the item might be hidden in CMS.
     */
    const VISIBILITY_VISIBLE = 'visible';

    /**
     * Denotes that the item is hidden.
     */
    const VISIBILITY_HIDDEN = 'hidden';

    /**
     * Denotes that the item is visible at certain time only, as configured by the scheduling
     * configuration.
     */
    const VISIBILITY_SCHEDULED = 'scheduled';

    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the collection to which the item belongs.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns the item definition.
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    public function getDefinition();

    /**
     * Returns if the item is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns the item position within the collection.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Returns the type of this item.
     *
     * Type can either be manual (inserted between items returned from the query),
     * or override (replaces the item from the query in the same position).
     *
     * @return int
     */
    public function getType();

    /**
     * Returns the value stored inside the collection item.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Returns the type of value stored inside the item.
     *
     * @return string
     */
    public function getValueType();

    /**
     * Returns if the item visibility is scheduled, as specified by item visibility/scheduling
     * configuration.
     *
     * @return bool
     */
    public function isScheduled();

    /**
     * Returns if the item is visible in provided point in time, as specified by item
     * visibility/scheduling configuration.
     *
     * If reference time is not provided, current time is used.
     *
     * @param \DateTimeInterface $reference
     *
     * @return string
     */
    public function isVisible(DateTimeInterface $reference = null);
}
