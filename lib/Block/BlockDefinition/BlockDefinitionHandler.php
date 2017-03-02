<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    const GROUP_CONTENT = 'content';
    const GROUP_DESIGN = 'design';

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    /**
     * Builds the placeholder parameters by using provided parameter builders.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface[] $builders
     */
    public function buildPlaceholderParameters(array $builders)
    {
    }

    /**
     * Builds the dynamic placeholder parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildDynamicPlaceholderParameters(ParameterBuilderInterface $builder)
    {
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
        return array();
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return false;
    }

    /**
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return false;
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return false;
    }

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers()
    {
        return array();
    }

    /**
     * Builds the parameters most blocks will use by using provided parameter builder.
     *
     * @param array $groups
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    protected function buildCommonParameters(ParameterBuilderInterface $builder, array $groups = array())
    {
        $builder->add(
            'css_class',
            ParameterType\TextLineType::class,
            array(
                'groups' => $groups,
            )
        );

        $builder->add(
            'css_id',
            ParameterType\TextLineType::class,
            array(
                'groups' => $groups,
            )
        );

        $builder->add(
            'set_container',
            ParameterType\BooleanType::class,
            array(
                'groups' => $groups,
            )
        );
    }
}
