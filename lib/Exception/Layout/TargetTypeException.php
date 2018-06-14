<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Layout;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class TargetTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $targetType
     *
     * @return \Netgen\BlockManager\Exception\Layout\TargetTypeException
     */
    public static function noTargetType(string $targetType): self
    {
        return new self(
            sprintf(
                'Target type "%s" does not exist.',
                $targetType
            )
        );
    }

    /**
     * @param string $targetType
     *
     * @return \Netgen\BlockManager\Exception\Layout\TargetTypeException
     */
    public static function noFormMapper(string $targetType): self
    {
        return new self(
            sprintf(
                'Form mapper for "%s" target type does not exist.',
                $targetType
            )
        );
    }
}
