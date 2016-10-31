<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ParameterStructValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    protected $parameterFilterRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface $parameterTypeRegistry
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface $parameterFilterRegistry
     */
    public function __construct(
        ParameterTypeRegistryInterface $parameterTypeRegistry,
        ParameterFilterRegistryInterface $parameterFilterRegistry
    ) {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
        $this->parameterFilterRegistry = $parameterFilterRegistry;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ParameterStructConstraint) {
            throw new UnexpectedTypeException($constraint, ParameterStructConstraint::class);
        }

        if (!$value instanceof ParameterStruct) {
            throw new UnexpectedTypeException($value, ParameterStruct::class);
        }

        $this->filterParameters($value, $constraint->parameters);

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->validate(
            $value->getParameters(),
            new Constraints\Collection(
                array(
                    'fields' => $this->buildConstraintFields(
                        $value,
                        $constraint->parameters,
                        $constraint->required
                    ),
                )
            )
        );
    }

    /**
     * Filters the parameter values.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct $parameterStruct
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     */
    protected function filterParameters(ParameterStruct $parameterStruct, array $parameters)
    {
        foreach ($parameterStruct->getParameters() as $parameterName => $parameter) {
            if (!isset($parameters[$parameterName])) {
                continue;
            }

            $filters = $this->parameterFilterRegistry->getParameterFilters($parameters[$parameterName]->getType());
            foreach ($filters as $filter) {
                $parameter = $filter->filter($parameter);
            }

            $parameterStruct->setParameter($parameterName, $parameter);
        }
    }

    /**
     * Builds the "fields" array from provided parameters and parameter values.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct $parameterStruct
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param bool $isRequired
     *
     * @return array
     */
    protected function buildConstraintFields(ParameterStruct $parameterStruct, array $parameters, $isRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            $parameterValue = $parameterStruct->hasParameter($parameterName) ?
                $parameterStruct->getParameter($parameterName) :
                null;

            $parameterType = $this->parameterTypeRegistry->getParameterType($parameter->getType());

            $fields[$parameterName] = $this->buildFieldConstraint(
                $parameterType->getConstraints($parameter, $parameterValue),
                $isRequired
            );

            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $subParameterName => $subParameter) {
                    $subParameterValue = $parameterStruct->hasParameter($subParameterName) ?
                        $parameterStruct->getParameter($subParameterName) :
                        null;

                    $subParameterType = $this->parameterTypeRegistry->getParameterType($subParameter->getType());
                    $constraints = $subParameterType->getValueConstraints($subParameter, $subParameterValue);

                    if (
                        $parameterStruct->hasParameter($parameterName) &&
                        $parameterStruct->getParameter($parameterName) &&
                        $subParameter->isRequired()
                    ) {
                        $constraints = array_merge(
                            $constraints,
                            $subParameterType->getRequiredConstraints($subParameter, $subParameterValue)
                        );
                    }

                    $fields[$subParameterName] = $this->buildFieldConstraint($constraints, $isRequired);
                }
            }
        }

        return $fields;
    }

    /**
     * Builds the Constraints\Required or Constraints\Optional constraint as specified by $isRequired flag.
     *
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @param bool $isRequired
     *
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function buildFieldConstraint(array $constraints, $isRequired)
    {
        return $isRequired ?
            new Constraints\Required($constraints) :
            new Constraints\Optional($constraints);
    }
}