<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Form\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Form\LinkType;
use Symfony\Component\Form\FormBuilderInterface;

class Link extends ParameterHandler
{
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
        $form->setDataMapper(new LinkDataMapper());
    }
}