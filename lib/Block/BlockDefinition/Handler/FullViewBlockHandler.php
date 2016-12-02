<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class FullViewBlockHandler extends TwigBlockHandler
{
    /**
     * @var string
     */
    protected $twigBlockName;

    /**
     * Constructor.
     *
     * @param string $twigBlockName
     */
    public function __construct($twigBlockName)
    {
        $this->twigBlockName = $twigBlockName;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $this->buildCommonParameters($builder);
    }

    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block)
    {
        return $this->twigBlockName;
    }
}
