<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Collection\Query;

class ContextualQueryIterator extends QueryIterator
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $limit
     */
    public function __construct(Query $query, $limit)
    {
        $this->limit = $limit;

        parent::__construct($query);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        $count = $this->query->getQueryType()->getInternalLimit($this->query);
        if ($count === null || $count > $this->limit) {
            $count = $this->limit;
        }

        return $count;
    }

    /**
     * Returns an iterator that iterates over the collection query.
     *
     * @return \Iterator
     */
    protected function buildIterator()
    {
        $queryValues = iterator_to_array(
            $this->generateSlots()
        );

        return new ArrayIterator($queryValues);
    }

    /**
     * Generates a dummy item.
     *
     * @return \Generator
     */
    protected function generateSlots()
    {
        for ($i = 0, $count = $this->count(); $i < $count; ++$i) {
            yield new Slot();
        }
    }
}