<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Block;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the locale of the block in the provided view
 * has a value specified in the configuration.
 */
final class Locale implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        return in_array($view->getBlock()->getLocale(), $config, true);
    }
}
