<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CollectionPagerExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ngbm_collection_pager',
                [CollectionPagerRuntime::class, 'renderCollectionPager'],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'ngbm_collection_page_url',
                [CollectionPagerRuntime::class, 'getCollectionPageUrl']
            ),
        ];
    }
}
