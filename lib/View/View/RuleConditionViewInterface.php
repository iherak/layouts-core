<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\ViewInterface;

interface RuleConditionViewInterface extends ViewInterface
{
    /**
     * Returns the condition.
     */
    public function getCondition(): Condition;
}
