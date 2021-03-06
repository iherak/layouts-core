<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

final class RuleCreateStruct
{
    /**
     * The ID of the layout to which the rule will be mapped.
     *
     * @var int|string|null
     */
    public $layoutId;

    /**
     * Priority of the rule.
     *
     * @var int|null
     */
    public $priority;

    /**
     * Specifies if the rule will be enabled or not.
     *
     * @var bool
     */
    public $enabled = false;

    /**
     * Description of the rule.
     *
     * @var string|null
     */
    public $comment;
}
