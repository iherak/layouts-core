<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\API;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class LayoutException extends InvalidArgumentException implements Exception
{
    public static function noZone(string $zone): self
    {
        return new self(
            sprintf(
                'Zone with "%s" identifier does not exist in the layout.',
                $zone
            )
        );
    }
}
