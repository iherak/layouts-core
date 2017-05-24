<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class QueryParameter implements ConditionTypeInterface
{
    /**
     * Returns the condition type.
     *
     * @return string
     */
    public function getType()
    {
        return 'query_parameter';
    }

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Collection(
                array(
                    'fields' => array(
                        'parameter_name' => new Constraints\Required(
                            array(
                                new Constraints\NotBlank(),
                                new Constraints\Type(array('type' => 'string')),
                            )
                        ),
                        'parameter_values' => new Constraints\Required(
                            array(
                                new Constraints\NotBlank(),
                                new Constraints\Type(array('type' => 'array')),
                                new Constraints\All(
                                    array(
                                        'constraints' => array(
                                            new Constraints\NotBlank(),
                                            new Constraints\Type(array('type' => 'scalar')),
                                        ),
                                    )
                                ),
                            )
                        ),
                    ),
                )
            ),
        );
    }

    /**
     * Returns if this request matches the provided value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $value
     *
     * @return bool
     */
    public function matches(Request $request, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value['parameter_name']) || empty($value['parameter_values'])) {
            return false;
        }

        $queryParameters = $request->query;
        if (!$queryParameters->has($value['parameter_name'])) {
            return false;
        }

        return in_array(
            $queryParameters->get($value['parameter_name']),
            $value['parameter_values'],
            true
        );
    }
}