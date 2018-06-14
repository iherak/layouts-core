<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Block;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class BlockTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockTypeException
     */
    public static function noBlockType(string $identifier): self
    {
        return new self(
            sprintf(
                'Block type with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockTypeException
     */
    public static function noBlockTypeGroup(string $identifier): self
    {
        return new self(
            sprintf(
                'Block type group with "%s" identifier does not exist.',
                $identifier
            )
        );
    }
}
