<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Mapper;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\LazyCollection;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Layout\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone as PersistenceZone;
use Netgen\BlockManager\Persistence\Values\Value as PersistenceValue;
use Netgen\BlockManager\Utils\DateTimeUtils;

final class LayoutMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutHandlerInterface $layoutHandler, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutHandler = $layoutHandler;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Builds the API zone value from persistence one.
     */
    public function mapZone(PersistenceZone $zone): Zone
    {
        $zoneData = [
            'identifier' => $zone->identifier,
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'linkedZone' => function () use ($zone): ?Zone {
                if ($zone->linkedLayoutId === null || $zone->linkedZoneIdentifier === null) {
                    return null;
                }

                try {
                    // We're always using published versions of linked zones
                    $linkedZone = $this->layoutHandler->loadZone(
                        $zone->linkedLayoutId,
                        PersistenceValue::STATUS_PUBLISHED,
                        $zone->linkedZoneIdentifier
                    );

                    return $this->mapZone($linkedZone);
                } catch (NotFoundException $e) {
                    return null;
                }
            },
        ];

        return Zone::fromArray($zoneData);
    }

    /**
     * Builds the API layout value from persistence one.
     */
    public function mapLayout(PersistenceLayout $layout): Layout
    {
        try {
            $layoutType = $this->layoutTypeRegistry->getLayoutType($layout->type);
        } catch (LayoutTypeException $e) {
            $layoutType = new NullLayoutType($layout->type);
        }

        $layoutData = [
            'id' => $layout->id,
            'layoutType' => $layoutType,
            'name' => $layout->name,
            'description' => $layout->description,
            'created' => DateTimeUtils::createFromTimestamp($layout->created),
            'modified' => DateTimeUtils::createFromTimestamp($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'mainLocale' => $layout->mainLocale,
            'availableLocales' => $layout->availableLocales,
            'zones' => new LazyCollection(
                function () use ($layout): array {
                    return array_map(
                        function (PersistenceZone $zone): Zone {
                            return $this->mapZone($zone);
                        },
                        $this->layoutHandler->loadLayoutZones($layout)
                    );
                }
            ),
        ];

        return Layout::fromArray($layoutData);
    }
}
