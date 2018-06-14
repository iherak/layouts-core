<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;

trait ParameterDefinitionCollectionTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    protected $parameterDefinitions = [];

    /**
     * Returns the list of parameter definitions in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    public function getParameterDefinitions(): array
    {
        return $this->parameterDefinitions;
    }

    /**
     * Returns the parameter definition with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition
     */
    public function getParameterDefinition(string $parameterName): ParameterDefinition
    {
        if ($this->hasParameterDefinition($parameterName)) {
            return $this->parameterDefinitions[$parameterName];
        }

        throw ParameterException::noParameterDefinition($parameterName);
    }

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameterDefinition(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameterDefinitions);
    }
}
