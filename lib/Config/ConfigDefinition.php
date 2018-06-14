<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\BlockManager\Value;

/**
 * @final
 */
class ConfigDefinition extends Value implements ConfigDefinitionInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    protected $handler;

    public function getConfigKey(): string
    {
        return $this->configKey;
    }
}
