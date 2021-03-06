<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception;

use Exception as BaseException;
use Throwable;

final class NotFoundException extends BaseException implements Exception
{
    /**
     * Creates a new not found exception.
     *
     * @param string $what
     * @param int|string $identifier
     * @param \Throwable $previous
     */
    public function __construct(string $what, $identifier = '', ?Throwable $previous = null)
    {
        $message = !empty($identifier) ?
            sprintf('Could not find %s with identifier "%s"', $what, $identifier) :
            sprintf('Could not find %s', $what);

        parent::__construct($message, 0, $previous);
    }
}
