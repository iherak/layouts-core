<?php

namespace Netgen\BlockManager\Parameters;

interface CompoundParameterTypeInterface extends ParameterTypeInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder);
}