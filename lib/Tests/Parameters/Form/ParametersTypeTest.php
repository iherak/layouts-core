<?php

namespace Netgen\BlockManager\Tests\Parameters\Form;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\Parameter\TextLine as TextLineParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametersTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $formMapper = new FormMapper(
            new ParameterFilterRegistry(),
            array(
                'text_line' => new TextLine(),
            )
        );

        return new ParametersType($formMapper);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'parameters' => array(
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ),
        );

        $updatedStruct = $this->getMockForAbstractClass(ParameterStruct::class);
        $updatedStruct->setParameter('css_id', 'Some CSS ID');
        $updatedStruct->setParameter('css_class', 'Some CSS class');

        $parentForm = $this->factory->create(
            FormType::class,
            $this->getMockForAbstractClass(ParameterStruct::class)
        );

        $parentForm->add(
            'parameters',
            ParametersType::class,
            array(
                'parameters' => array(
                    'css_class' => new TextLineParameter(),
                    'css_id' => new TextLineParameter(),
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

        self::assertArrayHasKey('parameters', $children);

        foreach (array_keys($submittedData['parameters']) as $key) {
            self::assertArrayHasKey($key, $children['parameters']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'parameters' => array(),
            'label_prefix' => 'label',
            'property_path_prefix' => 'parameters',
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertEquals(
            array(
                'inherit_data' => true,
                'parameters' => array(),
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'parameter_validation_groups' => null,
            ),
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingParameters()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\Form\ParametersType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('ngbm_parameters', $this->formType->getBlockPrefix());
    }
}