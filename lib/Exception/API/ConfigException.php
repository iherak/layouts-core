<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\API;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ConfigException extends InvalidArgumentException implements Exception
{
    public static function noConfig(string $configKey): self
    {
        return new self(
            sprintf(
                'Configuration with "%s" config key does not exist.',
                $configKey
            )
        );
    }

    public static function noConfigStruct(string $configKey): self
    {
        return new self(
            sprintf(
                'Config struct with config key "%s" does not exist.',
                $configKey
            )
        );
    }
}
