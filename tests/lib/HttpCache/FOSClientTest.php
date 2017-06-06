<?php

namespace Netgen\BlockManager\Tests\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use Netgen\BlockManager\HttpCache\FOSClient;
use Netgen\BlockManager\HttpCache\Layout\IdProviderInterface;
use PHPUnit\Framework\TestCase;

class FOSClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fosInvalidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $idProviderMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\FOSClient
     */
    protected $client;

    public function setUp()
    {
        $this->fosInvalidatorMock = $this->createMock(CacheInvalidator::class);
        $this->idProviderMock = $this->createMock(IdProviderInterface::class);

        $this->client = new FOSClient(
            $this->fosInvalidatorMock,
            $this->idProviderMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::__construct
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateLayouts
     */
    public function testInvalidateLayouts()
    {
        $this->idProviderMock
            ->expects($this->at(0))
            ->method('provideIds')
            ->with($this->equalTo(24))
            ->will($this->returnValue(array(24, 25, 26)));

        $this->idProviderMock
            ->expects($this->at(1))
            ->method('provideIds')
            ->with($this->equalTo(42))
            ->will($this->returnValue(array(42)));

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Layout-Id' => '^(24|25|26|42)$',
                    )
                )
            );

        $this->client->invalidateLayouts(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateLayouts
     */
    public function testInvalidateLayoutsWithEmptyLayoutIds()
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateLayouts(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateAllLayouts
     */
    public function testInvalidateAllLayouts()
    {
        $this->idProviderMock
            ->expects($this->never())
            ->method('provideIds');

        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Layout-Id' => '.*',
                    )
                )
            );

        $this->client->invalidateAllLayouts();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateBlocks
     */
    public function testInvalidateBlocks()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Block-Id' => '^(24|42)$',
                    )
                )
            );

        $this->client->invalidateBlocks(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateBlocks
     */
    public function testInvalidateBlocksWithEmptyBlockIds()
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateBlocks(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocks()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Origin-Layout-Id' => '^(24|42)$',
                    )
                )
            );

        $this->client->invalidateLayoutBlocks(array(24, 42));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateLayoutBlocks
     */
    public function testInvalidateLayoutBlocksWithEmptyLayoutIds()
    {
        $this->fosInvalidatorMock
            ->expects($this->never())
            ->method('invalidate');

        $this->client->invalidateLayoutBlocks(array());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\FOSClient::invalidateAllBlocks
     */
    public function testInvalidateAllBlocks()
    {
        $this->fosInvalidatorMock
            ->expects($this->once())
            ->method('invalidate')
            ->with(
                $this->equalTo(
                    array(
                        'X-Block-Id' => '.*',
                    )
                )
            );

        $this->client->invalidateAllBlocks();
    }
}
