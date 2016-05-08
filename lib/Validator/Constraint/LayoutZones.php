<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class LayoutZones extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Layout zone "%zoneIdentifier%" does not exist.';

    /**
     * @var string
     */
    public $zonesInvalidMessage = 'Zone identifiers are not an array.';

    /**
     * @var string
     */
    public $zoneMissingMessage = 'Zone "%zoneIdentifier%" is missing.';

    /**
     * @var string
     */
    public $layoutMissingMessage = 'Layout "%layoutType%" does not exist.';

    /**
     * @var string
     */
    public $layoutType;

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_layout_zones';
    }
}
