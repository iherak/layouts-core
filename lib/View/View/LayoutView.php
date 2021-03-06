<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\View\View;

final class LayoutView extends View implements LayoutViewInterface
{
    public function __construct(Layout $layout)
    {
        $this->parameters['layout'] = $layout;
    }

    public function getLayout(): Layout
    {
        return $this->parameters['layout'];
    }

    public static function getIdentifier(): string
    {
        return 'layout';
    }
}
