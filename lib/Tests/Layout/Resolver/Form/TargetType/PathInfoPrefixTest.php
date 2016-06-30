<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix as PathInfoPrefixMapper;
use Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PathInfoPrefixTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new PathInfoPrefix();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'path_info_prefix' => new PathInfoPrefixMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix::getOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => '/some/route',
        );

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = '/some/route';

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        self::assertArrayHasKey('value', $form->createView()->children);
    }
}