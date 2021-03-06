<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\HttpCache;

use Netgen\BlockManager\HttpCache\NullClient;
use PHPUnit\Framework\TestCase;

final class NullClientTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\NullClient
     */
    private $client;

    public function setUp(): void
    {
        $this->client = new NullClient();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateLayouts
     */
    public function testInvalidateLayouts(): void
    {
        $this->client->invalidateLayouts([24, 42]);

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts(): void
    {
        $this->client->invalidateAllLayouts();

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateBlocks
     */
    public function testInvalidateBlocks(): void
    {
        $this->client->invalidateBlocks([24, 42]);

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks(): void
    {
        $this->client->invalidateLayoutBlocks([24, 42]);

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks(): void
    {
        $this->client->invalidateAllBlocks();

        // Fake assertion to disable risky warning
        self::assertTrue(true);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\NullClient::commit
     */
    public function testCommit(): void
    {
        self::assertTrue($this->client->commit());
    }
}
