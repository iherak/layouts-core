<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\View;

final class RuleTargetView extends View implements RuleTargetViewInterface
{
    public function getTarget(): Target
    {
        return $this->parameters['target'];
    }

    public function getIdentifier(): string
    {
        return 'rule_target_view';
    }
}
