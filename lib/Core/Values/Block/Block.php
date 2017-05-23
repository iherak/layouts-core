<?php

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\ValueObject;

class Block extends ValueObject implements APIBlock
{
    use ParameterBasedValueTrait;
    use ConfigAwareValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var string
     */
    protected $viewType;

    /**
     * @var string
     */
    protected $itemViewType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    protected $placeholders = array();

    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $dynamicParameters;

    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Returns if the block is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType()
    {
        return $this->itemViewType;
    }

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Returns the specified placeholder.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the placeholder does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder($identifier)
    {
        if ($this->hasPlaceholder($identifier)) {
            return $this->placeholders[$identifier];
        }

        throw BlockException::noPlaceholder($identifier);
    }

    /**
     * Returns if blocks has a specified placeholder.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholder($identifier)
    {
        return isset($this->placeholders[$identifier]);
    }

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getDynamicParameter($parameter)
    {
        $this->buildDynamicParameters();

        if (!$this->hasDynamicParameter($parameter)) {
            return null;
        }

        if (!is_callable($this->dynamicParameters[$parameter])) {
            return $this->dynamicParameters[$parameter];
        }

        return $this->dynamicParameters[$parameter]();
    }

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasDynamicParameter($parameter)
    {
        $this->buildDynamicParameters();

        return array_key_exists($parameter, $this->dynamicParameters);
    }

    /**
     * Builds the dynamic parameters of the block from the definition.
     */
    protected function buildDynamicParameters()
    {
        if ($this->dynamicParameters === null) {
            $this->dynamicParameters = $this->definition->getDynamicParameters($this);
        }
    }
}
