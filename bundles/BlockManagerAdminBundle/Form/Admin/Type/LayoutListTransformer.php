<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\API\Values\Layout\LayoutList;
use Symfony\Component\Form\DataTransformerInterface;

final class LayoutListTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return new LayoutList($value);
    }
}
