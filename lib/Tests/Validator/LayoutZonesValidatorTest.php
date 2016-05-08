<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Validator\LayoutZonesValidator;
use Netgen\BlockManager\Validator\Constraint\LayoutZones;

class LayoutZonesValidatorTest extends ValidatorTest
{
    /**
     * @var \Netgen\BlockManager\Validator\LayoutZonesValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('layouts'))
            ->will($this->returnValue(array('layout' => array('zones' => array('zone' => array())))));

        $this->validator = new LayoutZonesValidator($this->configurationMock);
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidate()
    {
        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate(array('zone'), new LayoutZones(array('layoutType' => 'layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithNoLayout()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate(array('zone'), new LayoutZones(array('layoutType' => 'other_layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithInvalidZones()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate(42, new LayoutZones(array('layoutType' => 'layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithExtraZone()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate(array('zone', 'other_zone'), new LayoutZones(array('layoutType' => 'layout')));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutZonesValidator::validate
     */
    public function testValidateFailedWithMissingZone()
    {
        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate(array(), new LayoutZones(array('layoutType' => 'layout')));
    }
}
