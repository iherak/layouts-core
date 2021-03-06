<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\BlockManager\HttpCache\Layout\IdProviderInterface;

final class VarnishClient implements ClientInterface
{
    /**
     * @var \FOS\HttpCache\CacheInvalidator
     */
    private $fosInvalidator;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\IdProviderInterface
     */
    private $layoutIdProvider;

    public function __construct(CacheInvalidator $fosInvalidator, IdProviderInterface $layoutIdProvider)
    {
        $this->fosInvalidator = $fosInvalidator;
        $this->layoutIdProvider = $layoutIdProvider;
    }

    public function invalidateLayouts(array $layoutIds): void
    {
        if (empty($layoutIds)) {
            return;
        }

        $allLayoutIds = [];
        foreach ($layoutIds as $layoutId) {
            $allLayoutIds[] = $this->layoutIdProvider->provideIds($layoutId);
        }

        $this->fosInvalidator->invalidate(
            [
                'X-Layout-Id' => '^(' . implode('|', array_merge(...$allLayoutIds)) . ')$',
            ]
        );
    }

    public function invalidateAllLayouts(): void
    {
        $this->fosInvalidator->invalidate(
            [
                'X-Layout-Id' => '.*',
            ]
        );
    }

    public function invalidateBlocks(array $blockIds): void
    {
        if (empty($blockIds)) {
            return;
        }

        $this->fosInvalidator->invalidate(
            [
                'X-Block-Id' => '^(' . implode('|', $blockIds) . ')$',
            ]
        );
    }

    public function invalidateLayoutBlocks(array $layoutIds): void
    {
        if (empty($layoutIds)) {
            return;
        }

        $this->fosInvalidator->invalidate(
            [
                'X-Origin-Layout-Id' => '^(' . implode('|', $layoutIds) . ')$',
            ]
        );
    }

    public function invalidateAllBlocks(): void
    {
        $this->fosInvalidator->invalidate(
            [
                'X-Block-Id' => '.*',
            ]
        );
    }

    public function commit(): bool
    {
        try {
            $this->fosInvalidator->flush();
        } catch (ExceptionCollection $e) {
            // Do nothing, FOS invalidator will write to log.
            return false;
        }

        return true;
    }
}
