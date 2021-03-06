<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class ConfigDefinitionHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions(): array
    {
        return [
            'param' => ParameterDefinition::fromArray(
                [
                    'name' => 'param',
                    'type' => new ParameterType\TextLineType(),
                ]
            ),
            'param2' => ParameterDefinition::fromArray(
                [
                    'name' => 'param2',
                    'type' => new ParameterType\TextLineType(),
                ]
            ),
        ];
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
    }
}
