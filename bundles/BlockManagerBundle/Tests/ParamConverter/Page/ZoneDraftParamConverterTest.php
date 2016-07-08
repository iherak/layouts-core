<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneDraftParamConverter;
use Netgen\BlockManager\API\Values\Page\ZoneDraft as APIZoneDraft;
use Netgen\BlockManager\Core\Values\Page\ZoneDraft;
use Netgen\BlockManager\API\Service\LayoutService;
use PHPUnit\Framework\TestCase;

class ZoneDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new ZoneDraftParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('layoutId', 'zoneIdentifier'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('zone', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIZoneDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $zone = new ZoneDraft();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadZoneDraft')
            ->with($this->equalTo(42), $this->equalTo('left'))
            ->will($this->returnValue($zone));

        self::assertEquals(
            $zone,
            $this->paramConverter->loadValueObject(
                array(
                    'layoutId' => 42,
                    'zoneIdentifier' => 'left',
                )
            )
        );
    }
}