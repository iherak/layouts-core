<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class Boolean extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'boolean';
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getParameterConstraints(array $groups = null)
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'bool',
                ) + $this->getBaseConstraintOptions($groups)
            ),
        );
    }
}
