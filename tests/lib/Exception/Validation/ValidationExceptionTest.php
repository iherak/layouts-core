<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Validation;

use Netgen\BlockManager\Exception\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

final class ValidationExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Validation\ValidationException::validationFailed
     */
    public function testValidationFailed(): void
    {
        $exception = ValidationException::validationFailed('param', 'Some error');

        self::assertSame(
            'There was an error validating "param": Some error',
            $exception->getMessage()
        );
    }
}
