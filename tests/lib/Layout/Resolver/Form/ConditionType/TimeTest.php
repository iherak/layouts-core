<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\ConditionType\Time;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time as TimeMapper;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;

final class TimeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    public function setUp()
    {
        parent::setUp();

        $this->conditionType = new Time();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ConditionType(
            array(
                'time' => new TimeMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Time::getFormOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => array(
                'from' => array(
                    'datetime' => '2018-02-15T13:00',
                    'timezone' => 'Antarctica/Casey',
                ),
                'to' => array(
                    'datetime' => '2018-02-20T13:00',
                    'timezone' => 'Antarctica/Casey',
                ),
            ),
        );

        $updatedStruct = new ConditionCreateStruct();
        $updatedStruct->value = array(
            'from' => array(
                'datetime' => '2018-02-15 13:00:00',
                'timezone' => 'Antarctica/Casey',
            ),
            'to' => array(
                'datetime' => '2018-02-20 13:00:00',
                'timezone' => 'Antarctica/Casey',
            ),
        );

        $form = $this->factory->create(
            ConditionType::class,
            new ConditionCreateStruct(),
            array('conditionType' => $this->conditionType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(TimeType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}