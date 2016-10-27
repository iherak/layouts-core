<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class ItemLink extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.item_link.no_item';

    public $invalidItemMessage = 'netgen_block_manager.item_link.invalid_item';

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_item_link';
    }
}
