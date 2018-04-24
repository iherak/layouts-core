<?php

namespace Netgen\BlockManager\View\Matcher\Form\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\View\Matcher\Block\DefinitionTrait;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the block with the block definition equal to
 * value provided in the configuration.
 */
final class Definition implements MatcherInterface
{
    use DefinitionTrait;

    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        if (!$view->getForm()->getConfig()->hasOption('block')) {
            return false;
        }

        $block = $view->getForm()->getConfig()->getOption('block');
        if (!$block instanceof Block) {
            return false;
        }

        return $this->doMatch($block, $config);
    }
}
