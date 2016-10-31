<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\Validator\Constraints;

abstract class ParameterType implements ParameterTypeInterface
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Returns the parameter constraints.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(ParameterInterface $parameter, $value)
    {
        return array_merge(
            $this->getRequiredConstraints($parameter, $value),
            $this->getValueConstraints($parameter, $value)
        );
    }

    /**
     * Returns constraints that will be used when parameter is required.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getRequiredConstraints(ParameterInterface $parameter, $value)
    {
        if ($parameter->isRequired()) {
            return array(
                new Constraints\NotBlank(),
            );
        }

        return array();
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    abstract public function getValueConstraints(ParameterInterface $parameter, $value);

    /**
     * Converts the parameter value to from a domain format to scalar/hash format.
     *
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromValue($value)
    {
        return $value;
    }

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function toValue($value)
    {
        return $value;
    }

    /**
     * Returns if the parameter value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty($value)
    {
        return $value === null;
    }
}