<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class RuleUpdateStruct extends ValueObject
{
    /**
     * Set to 0 to remove the mapping.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * @var string
     */
    public $comment;
}