<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Generator;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockNormalizer extends Normalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Block\Block $block */
        $block = $object->getValue();
        $blockDefinition = $block->getDefinition();

        $parameters = $this->buildVersionedValues($block->getParameters(), $object->getVersion());
        $placeholders = $this->buildVersionedValues($block->getPlaceholders()->getValues(), $object->getVersion());

        $configuration = (function () use ($block, $object): Generator {
            foreach ($block->getConfigs() as $configKey => $config) {
                yield $configKey => $this->buildVersionedValues($config->getParameters(), $object->getVersion());
            }
        })();

        $data = [
            'id' => $block->getId(),
            'layout_id' => $block->getLayoutId(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'name' => $block->getName(),
            'parent_position' => $block->getPosition(),
            'parameters' => $this->normalizer->normalize($parameters, $format, $context),
            'view_type' => $block->getViewType(),
            'item_view_type' => $block->getItemViewType(),
            'published' => $block->isPublished(),
            'has_published_state' => $this->blockService->hasPublishedState($block),
            'locale' => $block->getLocale(),
            'is_translatable' => $block->isTranslatable(),
            'always_available' => $block->isAlwaysAvailable(),
            'is_container' => false,
            'placeholders' => $this->normalizer->normalize($placeholders, $format, $context),
            'collections' => $this->normalizer->normalize($this->getBlockCollections($block), $format, $context),
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];

        if ($blockDefinition instanceof ContainerDefinitionInterface) {
            $data['is_container'] = true;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Block && $data->getVersion() === Version::API_V1;
    }

    private function getBlockCollections(Block $block): Generator
    {
        foreach ($block->getCollections() as $identifier => $collection) {
            yield [
                'identifier' => $identifier,
                'collection_id' => $collection->getId(),
                'collection_type' => $collection->hasQuery() ? Collection::TYPE_DYNAMIC : Collection::TYPE_MANUAL,
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
            ];
        }
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
