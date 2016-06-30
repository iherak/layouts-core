<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $layoutTypeRegistry = new LayoutTypeRegistry();
        $layoutTypeRegistry->addLayoutType(new LayoutType('4_zones_a', true, '4 zones A', array()));

        return new CreateType($layoutTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::__construct
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'name' => 'My layout',
            'type' => '4_zones_a',
        );

        $updatedStruct = new LayoutCreateStruct();
        $updatedStruct->name = 'My layout';
        $updatedStruct->type = '4_zones_a';

        $form = $this->factory->create(
            CreateType::class,
            new LayoutCreateStruct()
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'data' => new LayoutCreateStruct(),
            )
        );

        self::assertEquals($options['data'], new LayoutCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'data' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('layout_create', $this->formType->getBlockPrefix());
    }
}