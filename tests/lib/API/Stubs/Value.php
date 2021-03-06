<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Value implements APIValue
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}
