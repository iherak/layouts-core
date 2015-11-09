<?php

namespace Netgen\BlockManager\Core\Service\Tests;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Exceptions\NotFoundException;

abstract class LayoutServiceTest extends ServiceTest
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::buildDomainLayoutObject
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::buildDomainZoneObject
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDateTime
     */
    public function testLoadLayout()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $layout);

        self::assertEquals(1, $layout->getId());
        self::assertNull($layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());

        self::assertInstanceOf('DateTime', $layout->getCreated());
        self::assertGreaterThan(0, $layout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $layout->getModified());
        self::assertGreaterThan(0, $layout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 1,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 2,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 3,
                        'layoutId' => $layout->getId(),
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $layout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testLoadLayoutThrowsInvalidArgumentExceptionOnInvalidId()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadLayout(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testLoadLayoutThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadLayout('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadLayout
     * @expectedException \Netgen\BlockManager\Exceptions\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadLayout(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     */
    public function testLoadZone()
    {
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
                )
            ),
            $layoutService->loadZone(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnInvalidId()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadZone(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testLoadZoneThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadZone('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::loadZone
     * @expectedException \Netgen\BlockManager\Exceptions\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $layoutService = $this->createLayoutService();
        $layoutService->loadZone(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayout()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            'new_layout',
            array('left', 'right')
        );

        $createdLayout = $layoutService->createLayout($layoutCreateStruct);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->getId());
        self::assertNull($createdLayout->getParentId());
        self::assertEquals('new_layout', $createdLayout->getIdentifier());

        self::assertInstanceOf('DateTime', $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'right',
                    )
                ),
            ),
            $createdLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     */
    public function testCreateLayoutWithParentId()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            'new_layout',
            array('left', 'right')
        );

        $parentLayout = $layoutService->loadLayout(1);
        $createdLayout = $layoutService->createLayout(
            $layoutCreateStruct,
            $parentLayout
        );

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->getId());
        self::assertEquals($parentLayout->getId(), $createdLayout->getParentId());
        self::assertEquals('new_layout', $createdLayout->getIdentifier());

        self::assertInstanceOf('DateTime', $createdLayout->getCreated());
        self::assertGreaterThan(0, $createdLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $createdLayout->getModified());
        self::assertGreaterThan(0, $createdLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $createdLayout->getId(),
                        'identifier' => 'right',
                    )
                ),
            ),
            $createdLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnInvalidIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(42, array('left'));
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('', array('left'));
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyZoneIdentifiers()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('new_layout', array());
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnInvalidZoneIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('new_layout', array('left', 42));
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateLayoutZoneThrowsInvalidArgumentExceptionOnEmptyZoneIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct('new_layout', array('left', ''));
        $layoutService->createLayout($layoutCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayout()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);
        $copiedLayout = $layoutService->copyLayout($layout);

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->getId());
        self::assertNull($copiedLayout->getParentId());
        self::assertEquals('copy_of_3_zones_a', $copiedLayout->getIdentifier());

        self::assertInstanceOf('DateTime', $copiedLayout->getCreated());
        self::assertGreaterThan(0, $copiedLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $copiedLayout->getModified());
        self::assertGreaterThan(0, $copiedLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $copiedLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     */
    public function testCopyLayoutWithProvidedIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);
        $copiedLayout = $layoutService->copyLayout($layout, 'new_layout_identifier');

        self::assertInstanceOf('Netgen\BlockManager\API\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->getId());
        self::assertNull($copiedLayout->getParentId());
        self::assertEquals('new_layout_identifier', $copiedLayout->getIdentifier());

        self::assertInstanceOf('DateTime', $copiedLayout->getCreated());
        self::assertGreaterThan(0, $copiedLayout->getCreated()->getTimestamp());

        self::assertInstanceOf('DateTime', $copiedLayout->getModified());
        self::assertGreaterThan(0, $copiedLayout->getModified()->getTimestamp());

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_left',
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'top_right',
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => $copiedLayout->getId(),
                        'identifier' => 'bottom',
                    )
                ),
            ),
            $copiedLayout->getZones()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCopyLayoutZoneThrowsInvalidArgumentExceptionOnInvalidIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);
        $layoutService->copyLayout($layout, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCopyLayoutZoneThrowsInvalidArgumentExceptionOnEmptyIdentifier()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);
        $layoutService->copyLayout($layout, '');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     */
    public function testDeleteLayout()
    {
        $layoutService = $this->createLayoutService();

        $layout = $layoutService->loadLayout(1);
        $layoutService->deleteLayout($layout);

        foreach ($layout->getZones() as $zone) {
            try {
                $layoutService->loadZone($zone->getId());
                $this->fail('Zone ' . $zone->getId() . ' not deleted when deleting layout.');
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        try {
            $layoutService->loadLayout($layout->getId());
            $this->fail('Layout was not deleted.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::newLayoutCreateStruct
     */
    public function testNewLayoutCreateStruct()
    {
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            new LayoutCreateStruct(
                array(
                    'layoutIdentifier' => 'new_layout',
                    'zoneIdentifiers' => array('left', 'right'),
                )
            ),
            $layoutService->newLayoutCreateStruct('new_layout', array('left', 'right'))
        );
    }
}
