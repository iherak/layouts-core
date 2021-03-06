<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Generator;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultNormalizer extends Normalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(UrlGeneratorInterface $urlGenerator, VisibilityResolverInterface $visibilityResolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->visibilityResolver = $visibilityResolver;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Collection\Result\Result $result */
        $result = $object->getValue();

        $mainItem = $result->getSubItem() instanceof CmsItemInterface ? $result->getSubItem() : $result->getItem();
        $overrideItem = $result->getSubItem() instanceof CmsItemInterface ? $result->getItem() : null;

        $data = $this->normalizeResultItem($mainItem, $object->getVersion(), $format, $context);
        $data['position'] = $result->getPosition();

        if ($overrideItem instanceof CmsItemInterface) {
            $data['override_item'] = $this->normalizeResultItem($overrideItem, $object->getVersion(), $format, $context);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Result && $data->getVersion() === Version::API_V1;
    }

    /**
     * Normalizes the provided result item into an array.
     */
    private function normalizeResultItem(CmsItemInterface $resultItem, int $version, ?string $format = null, array $context = []): array
    {
        $collectionItem = null;
        $cmsItem = $resultItem;
        $isDynamic = true;

        if ($resultItem instanceof ManualItem) {
            $collectionItem = $resultItem->getCollectionItem();
            $cmsItem = $collectionItem->getCmsItem();
            $isDynamic = false;
        }

        $configuration = (function () use ($collectionItem, $version): Generator {
            $itemConfigs = $collectionItem !== null ? $collectionItem->getConfigs() : [];
            foreach ($itemConfigs as $configKey => $config) {
                yield $configKey => $this->buildVersionedValues($config->getParameters(), $version);
            }
        })();

        return [
            'id' => $collectionItem !== null ? $collectionItem->getId() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId() : null,
            'visible' => $collectionItem !== null ?
                $this->visibilityResolver->isVisible($collectionItem) :
                true,
            'is_dynamic' => $isDynamic,
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'name' => $cmsItem->getName(),
            'cms_visible' => $cmsItem->isVisible(),
            'cms_url' => $this->urlGenerator->generate($cmsItem),
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];
    }

    /**
     * Builds the list of VersionedValue objects for provided list of values.
     */
    private function buildVersionedValues(iterable $values, int $version): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new VersionedValue($value, $version);
        }
    }
}
