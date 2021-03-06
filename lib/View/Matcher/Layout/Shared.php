<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Layout;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the shared flag of the layout in the provided view
 * matches the value provided in the configuration.
 */
final class Shared implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof LayoutViewInterface) {
            return false;
        }

        if (empty($config)) {
            return true;
        }

        return $view->getLayout()->isShared() === array_values($config)[0];
    }
}
