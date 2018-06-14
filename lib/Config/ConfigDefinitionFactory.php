<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

final class ConfigDefinitionFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the config definition.
     *
     * @param string $configKey
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface $handler
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function buildConfigDefinition(
        string $configKey,
        ConfigDefinitionHandlerInterface $handler
    ): ConfigDefinitionInterface {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        return new ConfigDefinition(
            [
                'configKey' => $configKey,
                'handler' => $handler,
                'parameterDefinitions' => $parameterDefinitions,
            ]
        );
    }
}
