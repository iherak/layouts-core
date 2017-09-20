<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

class Value
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * Constructor.
     *
     * @param int|string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->id < 100;
    }
}
