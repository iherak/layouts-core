<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use PHPUnit\Framework\TestCase;

final class ParameterStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ParameterStruct();
        self::assertSame('ngbm_parameter_struct', $constraint->validatedBy());
    }
}
