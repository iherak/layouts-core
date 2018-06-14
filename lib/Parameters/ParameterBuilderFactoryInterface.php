<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

interface ParameterBuilderFactoryInterface
{
    /**
     * Returns the new instance of parameter builder.
     *
     * @param array $config
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function createParameterBuilder(array $config = []): ParameterBuilderInterface;
}
