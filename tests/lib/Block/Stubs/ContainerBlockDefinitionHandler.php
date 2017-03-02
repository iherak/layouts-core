<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;

class ContainerBlockDefinitionHandler extends BaseBlockDefinitionHandler
{
    /**
     * @var array
     */
    protected $parameterGroups = array();

    /**
     * @var array
     */
    protected $placeholderIdentifiers = array();

    /**
     * Constructor.
     *
     * @param array $parameterGroups
     * @param array $placeholderIdentifiers
     */
    public function __construct($parameterGroups = array(), $placeholderIdentifiers = array('left', 'right'))
    {
        $this->parameterGroups = $parameterGroups;
        $this->placeholderIdentifiers = $placeholderIdentifiers;
    }

    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'css_class' => new Parameter('css_class', new ParameterType\TextLineType(), array(), false, null, $this->parameterGroups),
            'css_id' => new Parameter('css_id', new ParameterType\TextLineType(), array(), false, null, $this->parameterGroups),
        );
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return true;
    }

    /**
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return $this->placeholderIdentifiers;
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return array('definition_param' => 'definition_value');
    }
}
