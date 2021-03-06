<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Locale;

use Netgen\BlockManager\Exception\Exception;
use RuntimeException;

final class LocaleException extends RuntimeException implements Exception
{
    public static function noLocale(): self
    {
        return new self('No locales available in the current context.');
    }
}
