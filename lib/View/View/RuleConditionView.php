<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\View;

final class RuleConditionView extends View implements RuleConditionViewInterface
{
    public function __construct(Condition $condition)
    {
        $this->parameters['condition'] = $condition;
    }

    public function getCondition(): Condition
    {
        return $this->parameters['condition'];
    }

    public static function getIdentifier(): string
    {
        return 'rule_condition';
    }
}
