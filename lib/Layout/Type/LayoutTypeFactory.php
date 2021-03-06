<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Type;

final class LayoutTypeFactory
{
    /**
     * Builds the layout type.
     */
    public static function buildLayoutType(string $identifier, array $config): LayoutTypeInterface
    {
        $zones = [];

        foreach ($config['zones'] as $zoneIdentifier => $zoneConfig) {
            $zones[$zoneIdentifier] = Zone::fromArray(
                [
                    'identifier' => $zoneIdentifier,
                    'name' => $zoneConfig['name'],
                    'allowedBlockDefinitions' => $zoneConfig['allowed_block_definitions'],
                ]
            );
        }

        return LayoutType::fromArray(
            [
                'identifier' => $identifier,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'],
                'icon' => $config['icon'],
                'zones' => $zones,
            ]
        );
    }
}
