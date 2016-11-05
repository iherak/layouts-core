<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Parameter\TextLine;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompoundBooleanTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new TextLineMapper());

        return new CompoundBooleanType($formMapperRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'main_checkbox' => array(
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('main_checkbox', true);
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLine(),
                    'css_id' => new TextLine(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckbox()
    {
        $submittedData = array(
            'main_checkbox' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLine(),
                    'css_id' => new TextLine(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndEmptyData()
    {
        $submittedData = array(
            'main_checkbox' => array(),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('main_checkbox', false);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLine(),
                    'css_id' => new TextLine(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithReverseMode()
    {
        $submittedData = array(
            'main_checkbox' => array(
                '_self' => '1',
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('main_checkbox', true);

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLine(),
                    'css_id' => new TextLine(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'reverse' => true,
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildForm
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::buildView
     */
    public function testSubmitValidDataWithUncheckedCheckboxAndReverseMode()
    {
        $submittedData = array(
            'main_checkbox' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('main_checkbox', false);
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'main_checkbox',
            CompoundBooleanType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLine(),
                    'css_id' => new TextLine(),
                ),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'reverse' => true,
            )
        );

        $parentForm->submit($submittedData);

        $this->assertTrue($parentForm->isSynchronized());
        $this->assertEquals($updatedStruct, $parentForm->getData());

        $view = $parentForm->createView();
        $children = $view->children;

        $this->assertArrayHasKey('main_checkbox', $children);

        foreach (array_keys($submittedData['main_checkbox']) as $key) {
            $this->assertArrayHasKey($key, $children['main_checkbox']->children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'parameters' => array(),
            'label_prefix' => 'label',
            'property_path_prefix' => 'parameters',
            'reverse' => true,
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertTrue($resolvedOptions['inherit_data']);
        $this->assertEquals(array(), $resolvedOptions['parameters']);
        $this->assertEquals('label', $resolvedOptions['label_prefix']);
        $this->assertEquals('parameters', $resolvedOptions['property_path_prefix']);
        $this->assertTrue($resolvedOptions['reverse']);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'parameters' => null,
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'parameters' => array(),
                'label_prefix' => 'label',
                'property_path_prefix' => null,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\CompoundBooleanType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_compound_boolean', $this->formType->getBlockPrefix());
    }
}