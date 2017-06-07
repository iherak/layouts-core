<?php

namespace Netgen\BlockManager\Tests\HttpCache\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\HttpCache\Layout\IdProvider;
use PHPUnit\Framework\TestCase;

class IdProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\IdProvider
     */
    protected $idProvider;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->idProvider = new IdProvider($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::__construct
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIds()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will(
                $this->returnValue(
                    new Layout(
                        array(
                            'id' => 42,
                            'shared' => false,
                        )
                    )
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertEquals(array(42), $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithNonExistingLayout()
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will(
                $this->throwException(
                    new NotFoundException('layout', 42)
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertEquals(array(42), $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithSharedLayout()
    {
        $sharedLayout = new Layout(
            array(
                'id' => 42,
                'shared' => true,
            )
        );

        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue($sharedLayout));

        $this->layoutServiceMock
            ->expects($this->at(1))
            ->method('loadRelatedLayouts')
            ->with($this->equalTo($sharedLayout))
            ->will(
                $this->returnValue(
                    array(
                        new Layout(
                            array(
                                'id' => 43,
                            )
                        ),
                        new Layout(
                            array(
                                'id' => 44,
                            )
                        ),
                    )
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertEquals(array(42, 43, 44), $providedIds);
    }
}