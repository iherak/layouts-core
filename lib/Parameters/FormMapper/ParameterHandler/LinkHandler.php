<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Form\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Form\LinkType;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

class LinkHandler extends ParameterHandler
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $parameterType;

    /**
     * @var array
     */
    protected $defaultValueTypes;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     * @param array $defaultValueTypes
     */
    public function __construct(ParameterTypeInterface $parameterType, array $defaultValueTypes = array())
    {
        $this->parameterType = $parameterType;
        $this->defaultValueTypes = $defaultValueTypes;
    }

    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return LinkType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter)
    {
        $valueTypes = $parameter->getOptions()['value_types'];

        return array(
            'value_types' => !empty($valueTypes) ? $valueTypes : $this->defaultValueTypes,
        );
    }

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterInterface $parameter, $parameterName, array $options)
    {
        return array(
            'label' => false,
        ) + parent::getDefaultOptions($parameter, $parameterName, $options);
    }

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterInterface $parameter, FormBuilderInterface $form)
    {
        parent::handleForm($parameter, $form);

        $form->setDataMapper(new LinkDataMapper($this->parameterType));
    }
}