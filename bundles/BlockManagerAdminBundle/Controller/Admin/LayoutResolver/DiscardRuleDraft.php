<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class DiscardRuleDraft extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Discards a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function __invoke(Rule $rule)
    {
        $this->layoutResolverService->discardDraft($rule);

        $publishedRule = $this->layoutResolverService->loadRule($rule->getId());

        return $this->buildView($publishedRule, ViewInterface::CONTEXT_ADMIN);
    }
}