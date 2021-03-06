<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Form\Query;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\NullQueryType;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the query with the query type equal to
 * value provided in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        if (!$view->getForm()->getConfig()->hasOption('query')) {
            return false;
        }

        $query = $view->getForm()->getConfig()->getOption('query');
        if (!$query instanceof Query) {
            return false;
        }

        $queryType = $query->getQueryType();
        if ($queryType instanceof NullQueryType) {
            return in_array('null', $config, true);
        }

        return in_array($queryType->getType(), $config, true);
    }
}
