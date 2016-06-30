<?php

namespace Netgen\BlockManager\View\Matcher\RuleTarget;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\RuleTargetViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class Type implements MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $config
     *
     * @return bool
     */
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof RuleTargetViewInterface) {
            return false;
        }

        return in_array($view->getTarget()->getIdentifier(), $config);
    }
}